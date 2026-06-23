<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            if (! Schema::hasColumn('carts', 'selected_size')) {
                $table->unsignedTinyInteger('selected_size')->nullable()->after('product_id');
            }
        });

        $this->backfillCartSizes();

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'shoe_size')) {
                $table->dropColumn('shoe_size');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'shoe_size')) {
                $table->unsignedTinyInteger('shoe_size')->nullable()->after('phone');
            }
        });

        Schema::table('carts', function (Blueprint $table) {
            if (Schema::hasColumn('carts', 'selected_size')) {
                $table->dropColumn('selected_size');
            }
        });
    }

    private function backfillCartSizes(): void
    {
        if (in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement('
                UPDATE `carts` c
                LEFT JOIN `products` p ON p.`id` = c.`product_id`
                SET c.`selected_size` = COALESCE(c.`selected_size`, p.`size`)
            ');

            return;
        }

        DB::table('carts')
            ->whereNull('selected_size')
            ->orderBy('id')
            ->each(function ($cart) {
                $product = DB::table('products')->where('id', $cart->product_id)->first();

                if (! $product) {
                    return;
                }

                DB::table('carts')
                    ->where('id', $cart->id)
                    ->update(['selected_size' => $product->size]);
            });
    }
};
