<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            if (! Schema::hasColumn('transaction', 'snap_token')) {
                $table->string('snap_token')->nullable()->after('customer_phone');
            }

            if (! Schema::hasColumn('transaction', 'snap_redirect_url')) {
                $table->string('snap_redirect_url')->nullable()->after('snap_token');
            }

            if (! Schema::hasColumn('transaction', 'midtrans_transaction_id')) {
                $table->string('midtrans_transaction_id')->nullable()->after('snap_redirect_url');
            }

            if (! Schema::hasColumn('transaction', 'midtrans_payment_type')) {
                $table->string('midtrans_payment_type')->nullable()->after('midtrans_transaction_id');
            }

            if (! Schema::hasColumn('transaction', 'midtrans_status')) {
                $table->string('midtrans_status')->nullable()->after('midtrans_payment_type');
            }

            if (! Schema::hasColumn('transaction', 'midtrans_fraud_status')) {
                $table->string('midtrans_fraud_status')->nullable()->after('midtrans_status');
            }

            if (! Schema::hasColumn('transaction', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('midtrans_fraud_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            foreach ([
                'paid_at',
                'midtrans_fraud_status',
                'midtrans_status',
                'midtrans_payment_type',
                'midtrans_transaction_id',
                'snap_redirect_url',
                'snap_token',
            ] as $column) {
                if (Schema::hasColumn('transaction', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
