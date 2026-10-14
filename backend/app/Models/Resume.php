<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resume extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'language_id',
        'title',
        'status',
        'current_version_id',
    ];

    protected $casts = [
        'language_id' => 'integer',
        'current_version_id' => 'integer',
    ];

    /**
     * Resume belongs to one user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Resume belongs to one writing language.
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Resume has many versions.
     */
    public function versions(): HasMany
    {
        return $this->hasMany(ResumeVersion::class);
    }

    /**
     * Get the current version of the resume.
     */
    public function currentVersion(): BelongsTo
    {
        return $this->belongsTo(
            ResumeVersion::class,
            'current_version_id'
        );
    }
}