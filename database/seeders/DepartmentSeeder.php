<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Library',              'slug' => 'library',    'description' => 'Books, fines, and library card clearance'],
            ['name' => 'Finance / Bursar',      'slug' => 'finance',    'description' => 'Fee balance and financial obligations'],
            ['name' => 'Hostel / Dean of Students', 'slug' => 'hostel', 'description' => 'Room keys, equipment, and accommodation clearance'],
            ['name' => 'ICT Department',        'slug' => 'ict',        'description' => 'IT equipment and account deactivation'],
            ['name' => 'Faculty / Department',  'slug' => 'faculty',    'description' => 'Academic department clearance'],
            ['name' => 'Games & Sports',        'slug' => 'sports',     'description' => 'Sports equipment and club obligations'],
            ['name' => 'Academic Registrar',    'slug' => 'registrar',  'description' => 'Final academic records clearance'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(
                ['slug' => $dept['slug']],
                [
                    'name'        => $dept['name'],
                    'description' => $dept['description'],
                    'is_active'   => true,
                ]
            );
        }
    }
}