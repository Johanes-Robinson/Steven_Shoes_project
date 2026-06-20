<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! $this->canAlterShippingAddress()) {
            return;
        }

        DB::statement('ALTER TABLE `transaction` MODIFY `shipping_address` TEXT NOT NULL');
    }

    public function down(): void
    {
        if (! $this->canAlterShippingAddress()) {
            return;
        }

        DB::statement('ALTER TABLE `transaction` MODIFY `shipping_address` VARCHAR(255) NOT NULL');
    }

    private function canAlterShippingAddress(): bool
    {
        return Schema::hasTable('transaction')
            && Schema::hasColumn('transaction', 'shipping_address')
            && in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb'], true);
    }
};
