<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClearanceCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'certificate_number',
        'qr_code_path',
        'pdf_path',
        'verification_token',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }

    public function application()
    {
        return $this->belongsTo(ClearanceApplication::class, 'application_id');
    }
}
