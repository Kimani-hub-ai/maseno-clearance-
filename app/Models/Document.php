<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'department_id',
        'original_name',
        'stored_path',
        'mime_type',
        'file_size',
    ];

    public function application()
    {
        return $this->belongsTo(ClearanceApplication::class, 'application_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
