<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reg_number',
        'full_name',      // Used in clearance form and certificate
        'faculty',
        'department',
        'programme',
        'graduation_year',
        'phone',
        'status',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clearanceApplications()
    {
        return $this->hasMany(ClearanceApplication::class);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Get the most recent clearance application (with all relations for display).
     * Returns null if none exists.
     */
    public function currentApplication(): ?ClearanceApplication
    {
        return $this->clearanceApplications()
            ->with(['departmentClearances.department', 'documents', 'certificate'])
            ->latest('created_at')
            ->first();
    }

    /**
     * Display-friendly name: falls back to linked user name if full_name blank.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->full_name ?: ($this->user?->name ?? 'Unknown Student');
    }
}