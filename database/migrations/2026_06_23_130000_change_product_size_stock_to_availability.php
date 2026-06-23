<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_sizes', function (Blueprint $table) {
            if (! Schema::hasColumn('product_sizes', 'is_available')) {
                $table->boolean('is_available')->default(false)->after('size');
            }
        });

        if (Schema::hasColumn('product_sizes', 'stock')) {
            DB::table('product_sizes')->update([
                'is_available' => DB::raw('stock > 0'),
            ]);

            Schema::table('product_sizes', function (Blueprint $table) {
                $table->dropColumn('stock');
            });
        }
    }

    public function down(): void
    {
        Schema::table('product_sizes', function (Blueprint $table) {
            if (! Schema::hasColumn('product_sizes', 'stock')) {
                $table->unsignedInteger('stock')->default(0)->after('size');
            }
        });

        if (Schema::hasColumn('product_sizes', 'is_available')) {
            DB::table('product_sizes')->update([
                'stock' => DB::raw('CASE WHEN is_available THEN 1 ELSE 0 END'),
            ]);

            Schema::table('product_sizes', function (Blueprint $table) {
                $table->dropColumn('is_available');
            });
        }
    }
};
