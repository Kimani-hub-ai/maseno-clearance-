<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\DepartmentOfficer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Creates test accounts for every role so you can log in immediately.
     *
     * IMPORTANT: Change these passwords before deploying to production.
     */
    public function run(): void
    {
        // ─────────────────────────────────────────────
        // 1. ADMIN
        // ─────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@maseno.ac.ke'],
            [
                'name'      => 'System Administrator',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        // ─────────────────────────────────────────────
        // 2. REGISTRAR
        // ─────────────────────────────────────────────
        $registrar = User::firstOrCreate(
            ['email' => 'registrar@maseno.ac.ke'],
            [
                'name'      => 'Academic Registrar',
                'password'  => Hash::make('password'),
                'role'      => 'registrar',
                'is_active' => true,
            ]
        );

        // ─────────────────────────────────────────────
        // 3. ONE OFFICER PER DEPARTMENT
        // ─────────────────────────────────────────────
        // Make sure DepartmentSeeder has run first so departments exist.
        $departments = Department::all();

        if ($departments->isEmpty()) {
            $this->command->warn('No departments found — run DepartmentSeeder first. Skipping officer creation.');
            return;
        }

        foreach ($departments as $department) {
            $email = strtolower($department->slug) . '.officer@maseno.ac.ke';

            $officer = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'      => $department->name . ' Officer',
                    'password'  => Hash::make('password'),
                    'role'      => 'officer',
                    'is_active' => true,
                ]
            );

            // Link officer to their department
            DepartmentOfficer::firstOrCreate(
                ['user_id' => $officer->id],
                ['department_id' => $department->id]
            );
        }

        // ─────────────────────────────────────────────
        // Output a clean summary table
        // ─────────────────────────────────────────────
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->info('  TEST ACCOUNTS CREATED — Password for all: password');
        $this->command->info('═══════════════════════════════════════════════');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin',     'admin@maseno.ac.ke',     'password'],
                ['Registrar', 'registrar@maseno.ac.ke', 'password'],
                ...$departments->map(fn($d) => [
                    'Officer (' . $d->name . ')',
                    strtolower($d->slug) . '.officer@maseno.ac.ke',
                    'password',
                ])->toArray(),
            ]
        );
    }
}