<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class productController extends Controller
{
    public function dashboard(Request $request)
    {
        $this->authorizeAdmin($request);

        $products = Product::query()
            ->with('sizes')
            ->latest()
            ->get();

        $orders = Transaction::query()
            ->with(['user', 'details.product'])
            ->latest()
            ->get();

        $recentOrders = Transaction::query()
            ->with('user')
            ->whereIn('status', ['menunggu_pembayaran', 'diproses', 'dikirim'])
            ->latest()
            ->limit(5)
            ->get();

        $stats = [
            'total_revenue' => Transaction::query()
                ->where('status', '!=', 'batal')
                ->sum('total_amount'),
            'total_orders' => Transaction::count(),
            'total_products' => Product::count(),
            'total_customers' => User::query()
                ->whereNotIn(DB::raw('LOWER(email)'), User::adminEmails())
                ->count(),
        ];
        $sizeRange = Product::availableSizeRange();

        return view('Admin.Dashboard', compact('products', 'orders', 'recentOrders', 'stats', 'sizeRange'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'size' => ['nullable', 'integer', 'min:20', 'max:60'],
            'description' => ['required', 'string', 'max:2000'],
            'image' => ['required', 'image', 'max:2048'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'is_available' => ['required', 'boolean'],
            'size_available' => ['nullable', 'array'],
            'size_available.*' => ['nullable', 'boolean'],
        ]);

        $product = Product::create([
            'name' => $data['name'],
            'category' => $data['category'],
            'size' => $data['size'] ?? $this->defaultSizeFromAvailability($request),
            'description' => $data['description'],
            'image_url' => $this->storeProductImage($request),
            'price' => $data['price'],
            'is_available' => $data['is_available'],
        ]);

        if ($request->has('size_available')) {
            $this->syncSizeAvailability($product, $request);
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Produk baru berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeAdmin($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'size' => ['nullable', 'integer', 'min:20', 'max:60'],
            'description' => ['required', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'max:2048'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'is_available' => ['required', 'boolean'],
            'size_available' => ['nullable', 'array'],
            'size_available.*' => ['nullable', 'boolean'],
        ]);

        $imageUrl = $product->image_url;

        if ($request->hasFile('image')) {
            $this->deleteProductImage($product->image_url);
            $imageUrl = $this->storeProductImage($request);
        }

        $product->update([
            'name' => $data['name'],
            'category' => $data['category'],
            'size' => $data['size'] ?? ($request->has('size_available') ? $this->defaultSizeFromAvailability($request, $product->size) : $product->size),
            'description' => $data['description'],
            'image_url' => $imageUrl,
            'price' => $data['price'],
            'is_available' => $data['is_available'],
        ]);

        if ($request->has('size_available')) {
            $this->syncSizeAvailability($product, $request);
        }

        return back()->with('success', 'Produk berhasil diperbarui.');
    }

    public function updateSizeAvailability(Request $request, Product $product)
    {
        $this->authorizeAdmin($request);

        $request->validate([
            'size_available' => ['nullable', 'array'],
            'size_available.*' => ['nullable', 'boolean'],
        ]);

        $this->syncSizeAvailability($product, $request);

        $product->update([
            'size' => $this->defaultSizeFromAvailability($request, $product->size),
            'is_available' => in_array(true, $this->normalizedSizeAvailability($request), true),
        ]);

        return back()->with('success', 'Ketersediaan ukuran produk berhasil diperbarui.');
    }

    public function updateAvailability(Request $request, Product $product)
    {
        $this->authorizeAdmin($request);

        $data = $request->validate([
            'is_available' => ['required', 'boolean'],
        ]);

        $product->update([
            'is_available' => $data['is_available'],
        ]);

        return back()->with('success', 'Status ketersediaan produk berhasil diperbarui.');
    }

    public function destroy(Request $request, Product $product)
    {
        $this->authorizeAdmin($request);

        if (! $product->transactionDetails()->exists()) {
            $this->deleteProductImage($product->image_url);
        }

        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403);
    }

    private function storeProductImage(Request $request): string
    {
        $path = $request->file('image')->store('products', 'public');

        return '/storage/'.$path;
    }

    private function syncSizeAvailability(Product $product, Request $request): void
    {
        $now = now();

        foreach ($this->normalizedSizeAvailability($request) as $size => $isAvailable) {
            $product->sizes()->updateOrCreate(
                ['size' => $size],
                [
                    'is_available' => $isAvailable,
                    'updated_at' => $now,
                ]
            );
        }
    }

    private function normalizedSizeAvailability(Request $request): array
    {
        $availability = [];
        $input = $request->input('size_available', []);

        foreach (Product::availableSizeRange() as $size) {
            $availability[$size] = (bool) ($input[$size] ?? false);
        }

        return $availability;
    }

    private function defaultSizeFromAvailability(Request $request, int $fallback = 42): int
    {
        foreach ($this->normalizedSizeAvailability($request) as $size => $isAvailable) {
            if ($isAvailable) {
                return (int) $size;
            }
        }

        return $fallback;
    }

    private function deleteProductImage(?string $imageUrl): void
    {
        if (! $imageUrl || ! str_starts_with($imageUrl, '/storage/products/')) {
            return;
        }

        Storage::disk('public')->delete(str_replace('/storage/', '', $imageUrl));
    }
}
