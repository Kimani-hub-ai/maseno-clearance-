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
        'status',
        'submitted_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ClearanceStatus::class,
            'submitted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

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

    /**
     * Check if all department clearances are approved.
     */
    public function isFullyCleared(): bool
    {
        return $this->departmentClearances()
            ->where('status', '!=', 'approved')
            ->doesntExist();
    }

    /**
     * Check if any department has rejected.
     */
    public function hasRejection(): bool
    {
        return $this->departmentClearances()
            ->where('status', 'rejected')
            ->exists();
    }

    /**
     * Get progress as a percentage (approved / total departments).
     */
    public function progressPercentage(): int
    {
        $total = $this->departmentClearances()->count();
        if ($total === 0) {
            return 0;
        }
        $approved = $this->departmentClearances()->where('status', 'approved')->count();
        return (int) round(($approved / $total) * 100);
    }
}
