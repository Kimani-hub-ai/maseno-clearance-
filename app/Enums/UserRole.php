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
            self::Student   => 'Student',
            self::Officer   => 'Department Officer',
            self::Registrar => 'Academic Registrar',
            self::Admin     => 'System Administrator',
        };
    }

    public function canApprove(): bool
    {
        return in_array($this, [self::Officer, self::Registrar, self::Admin]);
    }
}
