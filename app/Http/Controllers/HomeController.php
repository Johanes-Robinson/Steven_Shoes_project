<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        $images = [];

        if (Schema::hasTable('products')) {
            $images = Product::query()
                ->where('is_available', true)
                ->whereHas('sizes', fn ($query) => $query->where('is_available', true))
                ->whereNotNull('image_url')
                ->latest()
                ->limit(10)
                ->pluck('image_url')
                ->all();
        }

        return view('welcome', compact('images'));
    }

    public function shop()
    {
        if (! Auth::check()) {
            return redirect()
                ->route('login')
                ->with('status', 'Silakan masuk terlebih dahulu untuk mulai berbelanja.');
        }

        return redirect()->route('customer.dashboard');
    }

    public function products()
    {
        $products = collect();

        if (Schema::hasTable('products')) {
            $products = Product::query()
                ->with('sizes')
                ->where('is_available', true)
                ->whereHas('sizes', fn ($query) => $query->where('is_available', true))
                ->latest()
                ->get();
        }

        return view('products', compact('products'));
    }

    public function location()
    {
        $location = [
            'name' => 'Steven Shoes - ITC Mangga Dua',
            'address' => 'ITC Mangga Dua Jakarta, Lantai Dasar',
            'query' => 'ITC Mangga Dua Jakarta Lantai Dasar',
        ];

        $location['maps_url'] = 'https://www.google.com/maps/search/?api=1&query='.urlencode($location['query']);
        $location['embed_url'] = 'https://www.google.com/maps?q='.urlencode($location['query']).'&output=embed';

        return view('location', compact('location'));
    }

    public function dashboard()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $products = Product::query()
            ->with('sizes')
            ->where('is_available', true)
            ->whereHas('sizes', fn ($query) => $query->where('is_available', true))
            ->latest()
            ->get();

        $cartItems = Cart::query()
            ->with('product')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $orders = Transaction::query()
            ->with(['details.product'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $cartSubtotal = $cartItems->sum(
            fn (Cart $item) => (float) ($item->product?->price ?? 0) * (int) $item->quantity
        );

        return view('customer.dashboard', compact('user', 'products', 'cartItems', 'orders', 'cartSubtotal'));
    }
}
