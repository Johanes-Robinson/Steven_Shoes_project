<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! $this->canAlterSessionsUserId()) {
            return;
        }

        DB::statement('ALTER TABLE `sessions` MODIFY `user_id` VARCHAR(255) NULL');
    }

    public function down(): void
    {
        if (! $this->canAlterSessionsUserId()) {
            return;
        }

        DB::statement('ALTER TABLE `sessions` MODIFY `user_id` BIGINT UNSIGNED NULL');
    }

    private function canAlterSessionsUserId(): bool
    {
        return Schema::hasTable('sessions')
            && Schema::hasColumn('sessions', 'user_id')
            && in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb'], true);
    }
};
