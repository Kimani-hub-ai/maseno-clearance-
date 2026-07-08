<?php

namespace App\Enums;

enum ClearanceStatus: string
{
    case Pending             = 'pending';
    case AwaitingRegistrar   = 'awaiting_registrar';
    case Approved            = 'approved';
    case Rejected            = 'rejected';

    public function label(): string
    {
        return match($this) {
            ClearanceStatus::Pending           => 'Pending',
            ClearanceStatus::AwaitingRegistrar => 'Awaiting Registrar',
            ClearanceStatus::Approved          => 'Approved',
            ClearanceStatus::Rejected          => 'Rejected',
        };
    }

    public function color(): string
    {
        return match($this) {
            ClearanceStatus::Pending           => 'yellow',
            ClearanceStatus::AwaitingRegistrar => 'blue',
            ClearanceStatus::Approved          => 'green',
            ClearanceStatus::Rejected          => 'red',
        };
    }
}