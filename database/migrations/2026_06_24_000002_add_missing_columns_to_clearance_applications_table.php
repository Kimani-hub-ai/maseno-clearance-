<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds application_type column so students can specify the purpose
 * of their application: graduation, deferral, transfer, withdrawal, other.
 *
 * Run: php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clearance_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('clearance_applications', 'application_type')) {
                $table->enum('application_type', [
                    'graduation',
                    'deferral',
                    'transfer',
                    'withdrawal',
                    'other',
                ])->default('graduation')->after('academic_year');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clearance_applications', function (Blueprint $table) {
            $table->dropColumn('application_type');
        });
    }
};