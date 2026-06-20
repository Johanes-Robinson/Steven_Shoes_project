<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transaction_details')) {
            Schema::table('transaction_details', function (Blueprint $table) {
                if (! $this->foreignKeyExists('transaction_details_transaction_id_foreign')) {
                    $table->foreign('transaction_id')
                        ->references('id')
                        ->on('transaction')
                        ->onDelete('cascade');
                }

                if (! $this->foreignKeyExists('transaction_details_product_id_foreign')) {
                    $table->foreign('product_id')
                        ->references('id')
                        ->on('products')
                        ->onDelete('cascade');
                }
            });

            return;
        }

        Schema::create('transaction_details', function (Blueprint $table) {
            $table->string('id')->primary();

            $table->string('transaction_id');
            $table->foreign('transaction_id')
                ->references('id')
                ->on('transaction')
                ->onDelete('cascade');

            $table->string('product_id');
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            $table->integer('quantity');
            $table->decimal('price', 12, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }

    private function foreignKeyExists(string $constraintName): bool
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return false;
        }

        return DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'transaction_details')
            ->where('CONSTRAINT_NAME', $constraintName)
            ->exists();
    }
};
