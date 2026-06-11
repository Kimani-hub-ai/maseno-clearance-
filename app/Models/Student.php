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
        'full_name',
        'faculty',
        'department',
        'programme',
        'graduation_year',
        'phone',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clearanceApplications()
    {
        return $this->hasMany(ClearanceApplication::class);
    }

    /**
     * Get the current academic year's application, if any.
     */
    public function currentApplication()
    {
        return $this->clearanceApplications()
            ->latest('created_at')
            ->first();
    }
}
