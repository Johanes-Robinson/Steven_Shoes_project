<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            if (! Schema::hasColumn('transaction', 'shipping_destination_postal_code')) {
                $table->string('shipping_destination_postal_code', 10)->nullable()->after('shipping_address');
            }

            if (! Schema::hasColumn('transaction', 'shipping_courier_code')) {
                $table->string('shipping_courier_code', 50)->nullable()->after('shipping_courier');
            }

            if (! Schema::hasColumn('transaction', 'shipping_service_code')) {
                $table->string('shipping_service_code', 50)->nullable()->after('shipping_courier_code');
            }

            if (! Schema::hasColumn('transaction', 'shipping_service_name')) {
                $table->string('shipping_service_name')->nullable()->after('shipping_service_code');
            }

            if (! Schema::hasColumn('transaction', 'shipping_estimation')) {
                $table->string('shipping_estimation')->nullable()->after('shipping_service_name');
            }

            if (! Schema::hasColumn('transaction', 'shipping_cost')) {
                $table->decimal('shipping_cost', 10, 2)->default(0)->after('shipping_estimation');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            foreach ([
                'shipping_cost',
                'shipping_estimation',
                'shipping_service_name',
                'shipping_service_code',
                'shipping_courier_code',
                'shipping_destination_postal_code',
            ] as $column) {
                if (Schema::hasColumn('transaction', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
