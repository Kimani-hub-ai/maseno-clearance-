<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function officers()
    {
        return $this->hasMany(DepartmentOfficer::class);
    }

    public function clearances()
    {
        return $this->hasMany(DepartmentClearance::class);
    }
}
