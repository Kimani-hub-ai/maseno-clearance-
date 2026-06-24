<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the columns that ClearanceService and ClearanceController
 * expect but were missing from the original migration:
 *   - academic_year  (string, e.g. "2025/2026")
 *   - submitted_at   (timestamp)
 *   - completed_at   (timestamp, nullable — only set when fully approved)
 *
 * Run:  php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clearance_applications', function (Blueprint $table) {
            // Only add if they don't already exist (safe to re-run)
            if (!Schema::hasColumn('clearance_applications', 'academic_year')) {
                $table->string('academic_year', 9)
                      ->nullable()
                      ->after('student_id')
                      ->comment('e.g. 2025/2026');
            }

            if (!Schema::hasColumn('clearance_applications', 'submitted_at')) {
                $table->timestamp('submitted_at')
                      ->nullable()
                      ->after('remarks');
            }

            if (!Schema::hasColumn('clearance_applications', 'completed_at')) {
                $table->timestamp('completed_at')
                      ->nullable()
                      ->after('submitted_at');
            }
        });

        // Back-fill existing rows so they have a valid academic_year
        // and submitted_at (use created_at as a proxy).
        \DB::table('clearance_applications')
            ->whereNull('academic_year')
            ->update([
                'academic_year' => '2024/2025',
                'submitted_at'  => \DB::raw('created_at'),
            ]);
    }

    public function down(): void
    {
        Schema::table('clearance_applications', function (Blueprint $table) {
            $table->dropColumn(['academic_year', 'submitted_at', 'completed_at']);
        });
    }
};