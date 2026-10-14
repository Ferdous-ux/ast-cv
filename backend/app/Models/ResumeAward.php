<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumeAward extends Model
{
    use HasFactory;

    protected $fillable = [
        'resume_version_id',
        'title',
        'issuer',
        'description',
        'awarded_at',
        'url',
        'sort_order',
    ];

    protected $casts = [
        'awarded_at' => 'date',
        'sort_order' => 'integer',
    ];

    public function resumeVersion(): BelongsTo
    {
        return $this->belongsTo(ResumeVersion::class);
    }
}