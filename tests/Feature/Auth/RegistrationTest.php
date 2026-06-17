<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    // This tells Laravel to spit out the exact validation error if it fails
    $this->withoutExceptionHandling();

    $response = $this->post('/register', [
        'name' => 'Test Student',
        'email' => 'student@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'student',
        'reg_number' => 'BIT/001/2026',
        'faculty' => 'School of Computing',
        'department' => 'IT',
        'programme' => 'BSc. IT',
        'graduation_year' => '2026',
        'phone' => '0712345678',
        'full_name' => 'Test Student Full Name',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect('/student/dashboard');
});