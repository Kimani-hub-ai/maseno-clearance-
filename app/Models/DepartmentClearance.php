<?php

namespace App\Models;

use App\Enums\DepartmentClearanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentClearance extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'department_id',
        'reviewed_by',
        'status',
        'remarks',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'status'      => DepartmentClearanceStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * The parent clearance application.
     *
     * IMPORTANT: Both relation names are defined here so that code using
     * either ->application() or ->clearanceApplication() works without
     * throwing RelationNotFoundException.
     */
    public function application()
    {
        return $this->belongsTo(ClearanceApplication::class, 'application_id');
    }

    /**
     * Alias for application() — the DepartmentClearanceController loads
     * this relation as 'clearanceApplication', so both names must exist.
     */
    public function clearanceApplication()
    {
        return $this->belongsTo(ClearanceApplication::class, 'application_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /**
     * Shortcut to the student's full name without extra eager-loading in views.
     * Usage: $checkpoint->student_full_name
     */
    public function getStudentFullNameAttribute(): string
    {
        return $this->clearanceApplication?->student?->full_name
            ?? $this->clearanceApplication?->student?->user?->name
            ?? 'Unknown Student';
    }

    /**
     * Shortcut to the student's registration number.
     * Usage: $checkpoint->student_reg_number
     */
    public function getStudentRegNumberAttribute(): string
    {
        return $this->clearanceApplication?->student?->reg_number ?? '—';
    }
}