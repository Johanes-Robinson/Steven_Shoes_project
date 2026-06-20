<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 30)->nullable()->after('role');
            }

            if (! Schema::hasColumn('users', 'shoe_size')) {
                $table->unsignedTinyInteger('shoe_size')->nullable()->after('phone');
            }

            if (! Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('shoe_size');
            }
        });

        Schema::table('transaction', function (Blueprint $table) {
            if (! Schema::hasColumn('transaction', 'shipping_courier')) {
                $table->string('shipping_courier')->default('JNE')->after('shipping_address');
            }
        });

        if (Schema::hasColumn('transaction', 'cart_id') && Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('transaction', function (Blueprint $table) {
                $table->dropForeign(['cart_id']);
            });

            Schema::table('transaction', function (Blueprint $table) {
                $table->string('cart_id')->nullable()->change();
            });

            Schema::table('transaction', function (Blueprint $table) {
                $table->foreign('cart_id')
                    ->references('id')
                    ->on('carts')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('transaction', 'cart_id') && Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('transaction', function (Blueprint $table) {
                $table->dropForeign(['cart_id']);
            });

            Schema::table('transaction', function (Blueprint $table) {
                $table->string('cart_id')->nullable(false)->change();
            });

            Schema::table('transaction', function (Blueprint $table) {
                $table->foreign('cart_id')
                    ->references('id')
                    ->on('carts')
                    ->cascadeOnDelete();
            });
        }

        Schema::table('transaction', function (Blueprint $table) {
            if (Schema::hasColumn('transaction', 'shipping_courier')) {
                $table->dropColumn('shipping_courier');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'address')) {
                $table->dropColumn('address');
            }

            if (Schema::hasColumn('users', 'shoe_size')) {
                $table->dropColumn('shoe_size');
            }

            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
        });
    }
};
