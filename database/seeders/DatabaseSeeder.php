<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Order matters: departments must exist before officers can be linked to them
        $this->call([
            DepartmentSeeder::class,
            UserSeeder::class,
        ]);
    }
}