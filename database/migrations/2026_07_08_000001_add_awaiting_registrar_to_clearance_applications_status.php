<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE clearance_applications
            MODIFY COLUMN status
            ENUM('pending','awaiting_registrar','approved','rejected')
            NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        DB::statement("
            UPDATE clearance_applications
            SET status = 'pending'
            WHERE status = 'awaiting_registrar'
        ");

        DB::statement("
            ALTER TABLE clearance_applications
            MODIFY COLUMN status
            ENUM('pending','approved','rejected')
            NOT NULL DEFAULT 'pending'
        ");
    }
};
