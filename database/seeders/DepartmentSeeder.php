<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name'        => 'Finance / Bursar',
                'slug'        => 'finance',
                'description' => 'Verifies all fee payments and financial obligations are cleared.',
            ],
            [
                'name'        => 'Library',
                'slug'        => 'library',
                'description' => 'Confirms no outstanding books, fines, or borrowed items.',
            ],
            [
                'name'        => 'Hostel / Dean of Students',
                'slug'        => 'hostel',
                'description' => 'Confirms room has been vacated and no property damage.',
            ],
            [
                'name'        => 'ICT Department',
                'slug'        => 'ict',
                'description' => 'Confirms return of any ICT equipment issued to the student.',
            ],
            [
                'name'        => 'Faculty / Department',
                'slug'        => 'faculty',
                'description' => 'Confirms all coursework, projects, and academic requirements are met.',
            ],
            [
                'name'        => 'Games & Sports',
                'slug'        => 'games-sports',
                'description' => 'Confirms return of any sports equipment and club obligations.',
            ],
            [
                'name'        => 'Academic Registrar',
                'slug'        => 'registrar',
                'description' => 'Final validation of eligibility and overall clearance approval.',
            ],
        ];

        foreach ($departments as $dept) {
            DB::table('departments')->updateOrInsert(
                ['slug' => $dept['slug']],
                array_merge($dept, [
                    'is_active'  => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ])
            );
        }
    }
}
