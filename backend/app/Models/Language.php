<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'native_name',
        'direction',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Language is used as a spoken language
     * in resume versions.
     */
    public function resumeVersions(): BelongsToMany
    {
        return $this->belongsToMany(
            ResumeVersion::class,
            'resume_languages',
            'language_id',
            'resume_version_id'
        )->withPivot([
            'proficiency',
            'sort_order',
        ])->withTimestamps();
    }

    /**
     * Language is used as the writing language
     * of resumes.
     */
    public function resumes()
    {
        return $this->hasMany(Resume::class);
    }
}