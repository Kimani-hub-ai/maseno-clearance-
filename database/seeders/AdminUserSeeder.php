<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@maseno.ac.ke'],
            [
                'name'       => 'System Administrator',
                'email'      => 'admin@maseno.ac.ke',
                'password'   => Hash::make('ChangeMe@2024!'),
                'role'       => 'admin',
                'is_active'  => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'registrar@maseno.ac.ke'],
            [
                'name'       => 'Academic Registrar',
                'email'      => 'registrar@maseno.ac.ke',
                'password'   => Hash::make('ChangeMe@2024!'),
                'role'       => 'registrar',
                'is_active'  => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );
    }
}
