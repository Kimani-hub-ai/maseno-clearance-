<?php

namespace App\Enums;

enum UserRole: string
{
    case Student   = 'student';
    case Officer   = 'officer';
    case Registrar = 'registrar';
    case Admin     = 'admin';

    public function label(): string
    {
        return match($this) {
            UserRole::Student   => 'Student',
            UserRole::Officer   => 'Department Officer',
            UserRole::Registrar => 'Registrar',
            UserRole::Admin     => 'Administrator',
        };
    }
}