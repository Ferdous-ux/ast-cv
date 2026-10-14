<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumeLanguage extends Model
{
    use HasFactory;

    protected $fillable = [
        'resume_version_id',
        'language_id',
        'proficiency',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function resumeVersion(): BelongsTo
    {
        return $this->belongsTo(ResumeVersion::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}