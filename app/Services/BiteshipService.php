<?php

namespace App\Services;

use App\Models\Cart;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class BiteshipService
{
    public function isConfigured(): bool
    {
        return filled($this->apiKey()) && filled($this->originPostalCode());
    }

    public function rates(Collection $cartItems, string $destinationPostalCode): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Biteship belum dikonfigurasi.');
        }

        $response = Http::withToken($this->apiKey())
            ->acceptJson()
            ->asJson()
            ->timeout(15)
            ->post($this->baseUrl().'/v1/rates/couriers', [
                'origin_postal_code' => $this->originPostalCode(),
                'destination_postal_code' => $destinationPostalCode,
                'couriers' => $this->couriers(),
                'items' => $this->items($cartItems),
            ]);

        if (! $response->successful()) {
            throw new RuntimeException($response->json('error') ?: 'Gagal mengambil ongkir Biteship.');
        }

        return collect($response->json('pricing', []))
            ->map(fn (array $rate) => $this->normalizeRate($rate))
            ->filter(fn (array $rate) => filled($rate['service_key']) && $rate['price'] >= 0)
            ->values()
            ->all();
    }

    public function findRate(Collection $cartItems, string $destinationPostalCode, string $serviceKey): ?array
    {
        return collect($this->rates($cartItems, $destinationPostalCode))
            ->firstWhere('service_key', $serviceKey);
    }

    private function normalizeRate(array $rate): array
    {
        $courierCode = (string) Arr::get($rate, 'courier_code', '');
        $serviceCode = (string) Arr::get($rate, 'courier_service_code', '');

        return [
            'service_key' => Str::lower($courierCode.'|'.$serviceCode),
            'courier_code' => $courierCode,
            'courier_name' => (string) Arr::get($rate, 'courier_name', Str::upper($courierCode)),
            'service_code' => $serviceCode,
            'service_name' => (string) Arr::get($rate, 'courier_service_name', Str::upper($serviceCode)),
            'description' => (string) Arr::get($rate, 'description', ''),
            'duration' => (string) Arr::get($rate, 'duration', ''),
            'price' => (int) Arr::get($rate, 'price', 0),
        ];
    }

    private function items(Collection $cartItems): array
    {
        return $cartItems
            ->filter(fn (Cart $item) => $item->product !== null)
            ->map(fn (Cart $item) => [
                'name' => Str::limit($item->product->name, 40, ''),
                'description' => Str::limit($item->product->description ?? $item->product->name, 80, ''),
                'value' => (int) round((float) $item->product->price),
                'quantity' => (int) $item->quantity,
                'weight' => (int) config('services.biteship.default_weight', 1000),
                'length' => (int) config('services.biteship.default_length', 35),
                'width' => (int) config('services.biteship.default_width', 25),
                'height' => (int) config('services.biteship.default_height', 15),
            ])
            ->values()
            ->all();
    }

    private function apiKey(): ?string
    {
        return config('services.biteship.api_key');
    }

    private function baseUrl(): string
    {
        return rtrim((string) config('services.biteship.base_url', 'https://api.biteship.com'), '/');
    }

    private function originPostalCode(): ?string
    {
        return config('services.biteship.origin_postal_code');
    }

    private function couriers(): string
    {
        return collect(explode(',', (string) config('services.biteship.couriers', 'jne,jnt,sicepat')))
            ->map(fn (string $courier) => trim($courier))
            ->filter()
            ->implode(',');
    }
}
