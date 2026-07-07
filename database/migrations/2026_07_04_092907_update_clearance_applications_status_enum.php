<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE clearance_applications MODIFY COLUMN status 
            ENUM('draft','submitted','in_progress','cleared','rejected','pending','approved') 
            NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE clearance_applications MODIFY COLUMN status 
            ENUM('draft','submitted','in_progress','cleared','rejected') 
            NOT NULL DEFAULT 'draft'");
    }
};