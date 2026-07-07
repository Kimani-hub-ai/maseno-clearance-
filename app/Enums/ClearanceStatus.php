<?php

namespace App\Enums;

enum ClearanceStatus: string
{
    // Developer A track — used by ClearanceService, student portal, department workflow
    case Draft      = 'draft';
    case Submitted  = 'submitted';
    case InProgress = 'in_progress';
    case Cleared    = 'cleared';

    // Developer B track — used by RegistrarController and registrar dashboard
    case Pending  = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::Draft      => 'Draft',
            self::Submitted  => 'Submitted',
            self::InProgress => 'In Progress',
            self::Cleared    => 'Cleared',
            self::Pending    => 'Pending',
            self::Approved   => 'Approved',
            self::Rejected   => 'Rejected',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Draft      => 'gray',
            self::Submitted  => 'blue',
            self::InProgress => 'yellow',
            self::Cleared    => 'green',
            self::Pending    => 'yellow',
            self::Approved   => 'green',
            self::Rejected   => 'red',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Cleared, self::Approved, self::Rejected]);
    }
}