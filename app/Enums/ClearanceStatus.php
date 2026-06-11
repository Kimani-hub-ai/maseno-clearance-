<?php

namespace App\Enums;

enum ClearanceStatus: string
{
    case Draft      = 'draft';
    case Submitted  = 'submitted';
    case InProgress = 'in_progress';
    case Cleared    = 'cleared';
    case Rejected   = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::Draft      => 'Draft',
            self::Submitted  => 'Submitted',
            self::InProgress => 'In Progress',
            self::Cleared    => 'Cleared',
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
            self::Rejected   => 'red',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Cleared, self::Rejected]);
    }
}
