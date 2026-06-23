<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->string('product_id');
            $table->unsignedTinyInteger('size');
            $table->boolean('is_available')->default(false);
            $table->timestamps();

            $table->unique(['product_id', 'size']);
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });

        $this->backfillExistingProducts();
    }

    public function down(): void
    {
        Schema::dropIfExists('product_sizes');
    }

    private function backfillExistingProducts(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        DB::table('products')
            ->orderBy('id')
            ->each(function ($product) {
                $now = now();
                $rows = collect(Product::availableSizeRange())
                    ->map(fn (int $size) => [
                        'product_id' => $product->id,
                        'size' => $size,
                        'is_available' => (int) $product->size === $size && (bool) $product->is_available,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])
                    ->all();

                DB::table('product_sizes')->insertOrIgnore($rows);
            });
    }
};
