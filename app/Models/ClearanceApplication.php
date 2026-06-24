<?php

namespace App\Models;

use App\Enums\ClearanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClearanceApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year',
        'application_type',  // graduation|deferral|transfer|withdrawal|other
        'status',
        'remarks',
        'submitted_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status'       => ClearanceStatus::class,
            'submitted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function departmentClearances()
    {
        return $this->hasMany(DepartmentClearance::class, 'application_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'application_id');
    }

    public function certificate()
    {
        return $this->hasOne(ClearanceCertificate::class, 'application_id');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isFullyCleared(): bool
    {
        return $this->departmentClearances()
            ->where('status', '!=', 'approved')
            ->doesntExist();
    }

    public function hasRejection(): bool
    {
        return $this->departmentClearances()
            ->where('status', 'rejected')
            ->exists();
    }

    public function progressPercentage(): int
    {
        $total = $this->departmentClearances()->count();
        if ($total === 0) return 0;
        $approved = $this->departmentClearances()->where('status', 'approved')->count();
        return (int) round(($approved / $total) * 100);
    }

    /**
     * Student's full name — pulled from student profile or user account.
     */
    public function getStudentFullNameAttribute(): string
    {
        return $this->student?->full_name
            ?? $this->student?->user?->name
            ?? 'Unknown Student';
    }

    /**
     * Human-readable label for the application type.
     */
    public function getApplicationTypeLabelAttribute(): string
    {
        return match($this->application_type) {
            'graduation' => 'Graduation Clearance',
            'deferral'   => 'Deferral of Studies',
            'transfer'   => 'Transfer to Another Institution',
            'withdrawal' => 'Withdrawal from University',
            default      => 'Other',
        };
    }
}