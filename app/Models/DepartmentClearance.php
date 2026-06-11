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
            'status' => DepartmentClearanceStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function application()
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
}
