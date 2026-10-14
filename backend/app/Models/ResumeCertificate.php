<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumeCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'resume_version_id',
        'name',
        'issuer',
        'credential_id',
        'credential_url',
        'issued_at',
        'expires_at',
        'sort_order',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'expires_at' => 'date',
        'sort_order' => 'integer',
    ];

    public function resumeVersion(): BelongsTo
    {
        return $this->belongsTo(ResumeVersion::class);
    }
}