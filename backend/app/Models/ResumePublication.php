<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumePublication extends Model
{
    use HasFactory;

    protected $fillable = [
        'resume_version_id',
        'title',
        'publisher',
        'description',
        'published_at',
        'url',
        'sort_order',
    ];

    protected $casts = [
        'published_at' => 'date',
        'sort_order' => 'integer',
    ];

    public function resumeVersion(): BelongsTo
    {
        return $this->belongsTo(ResumeVersion::class);
    }
}