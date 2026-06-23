<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addSnapshotColumns();
        $this->backfillSnapshots();
        $this->preserveTransactionsOnDelete();
    }

    public function down(): void
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            if (Schema::hasColumn('transaction_details', 'product_image_url')) {
                $table->dropColumn('product_image_url');
            }

            if (Schema::hasColumn('transaction_details', 'product_size')) {
                $table->dropColumn('product_size');
            }

            if (Schema::hasColumn('transaction_details', 'product_category')) {
                $table->dropColumn('product_category');
            }

            if (Schema::hasColumn('transaction_details', 'product_name')) {
                $table->dropColumn('product_name');
            }
        });

        Schema::table('transaction', function (Blueprint $table) {
            if (Schema::hasColumn('transaction', 'customer_phone')) {
                $table->dropColumn('customer_phone');
            }

            if (Schema::hasColumn('transaction', 'customer_email')) {
                $table->dropColumn('customer_email');
            }

            if (Schema::hasColumn('transaction', 'customer_name')) {
                $table->dropColumn('customer_name');
            }
        });
    }

    private function addSnapshotColumns(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            if (! Schema::hasColumn('transaction', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('shipping_courier');
            }

            if (! Schema::hasColumn('transaction', 'customer_email')) {
                $table->string('customer_email')->nullable()->after('customer_name');
            }

            if (! Schema::hasColumn('transaction', 'customer_phone')) {
                $table->string('customer_phone', 30)->nullable()->after('customer_email');
            }
        });

        Schema::table('transaction_details', function (Blueprint $table) {
            if (! Schema::hasColumn('transaction_details', 'product_name')) {
                $table->string('product_name')->nullable()->after('price');
            }

            if (! Schema::hasColumn('transaction_details', 'product_category')) {
                $table->string('product_category')->nullable()->after('product_name');
            }

            if (! Schema::hasColumn('transaction_details', 'product_size')) {
                $table->unsignedInteger('product_size')->nullable()->after('product_category');
            }

            if (! Schema::hasColumn('transaction_details', 'product_image_url')) {
                $table->string('product_image_url')->nullable()->after('product_size');
            }
        });
    }

    private function backfillSnapshots(): void
    {
        if ($this->isMysqlLike()) {
            DB::statement("
                UPDATE `transaction` t
                LEFT JOIN `users` u ON u.`id` = t.`user_id`
                SET
                    t.`customer_name` = COALESCE(t.`customer_name`, u.`name`),
                    t.`customer_email` = COALESCE(t.`customer_email`, u.`email`),
                    t.`customer_phone` = COALESCE(t.`customer_phone`, u.`phone`)
            ");

            DB::statement("
                UPDATE `transaction_details` td
                LEFT JOIN `products` p ON p.`id` = td.`product_id`
                SET
                    td.`product_name` = COALESCE(td.`product_name`, p.`name`),
                    td.`product_category` = COALESCE(td.`product_category`, p.`category`),
                    td.`product_size` = COALESCE(td.`product_size`, p.`size`),
                    td.`product_image_url` = COALESCE(td.`product_image_url`, p.`image_url`)
            ");

            return;
        }

        DB::table('transaction')
            ->whereNull('customer_name')
            ->orderBy('id')
            ->each(function ($transaction) {
                $user = DB::table('users')->where('id', $transaction->user_id)->first();

                if (! $user) {
                    return;
                }

                DB::table('transaction')
                    ->where('id', $transaction->id)
                    ->update([
                        'customer_name' => $user->name,
                        'customer_email' => $user->email,
                        'customer_phone' => $user->phone ?? null,
                    ]);
            });

        DB::table('transaction_details')
            ->whereNull('product_name')
            ->orderBy('id')
            ->each(function ($detail) {
                $product = DB::table('products')->where('id', $detail->product_id)->first();

                if (! $product) {
                    return;
                }

                DB::table('transaction_details')
                    ->where('id', $detail->id)
                    ->update([
                        'product_name' => $product->name,
                        'product_category' => $product->category,
                        'product_size' => $product->size,
                        'product_image_url' => $product->image_url,
                    ]);
            });
    }

    private function preserveTransactionsOnDelete(): void
    {
        if (! $this->isMysqlLike()) {
            return;
        }

        Schema::table('transaction', function (Blueprint $table) {
            if ($this->foreignKeyExists('transaction', 'transaction_user_id_foreign')) {
                $table->dropForeign(['user_id']);
            }
        });

        DB::statement('ALTER TABLE `transaction` MODIFY `user_id` VARCHAR(255) NULL');

        Schema::table('transaction', function (Blueprint $table) {
            if (! $this->foreignKeyExists('transaction', 'transaction_user_id_foreign')) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->nullOnDelete();
            }
        });

        Schema::table('transaction_details', function (Blueprint $table) {
            if ($this->foreignKeyExists('transaction_details', 'transaction_details_product_id_foreign')) {
                $table->dropForeign(['product_id']);
            }
        });

        DB::statement('ALTER TABLE `transaction_details` MODIFY `product_id` VARCHAR(255) NULL');

        Schema::table('transaction_details', function (Blueprint $table) {
            if (! $this->foreignKeyExists('transaction_details', 'transaction_details_product_id_foreign')) {
                $table->foreign('product_id')
                    ->references('id')
                    ->on('products')
                    ->nullOnDelete();
            }
        });
    }

    private function foreignKeyExists(string $table, string $constraintName): bool
    {
        if (! $this->isMysqlLike()) {
            return false;
        }

        return DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraintName)
            ->exists();
    }

    private function isMysqlLike(): bool
    {
        return in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb'], true);
    }
};
