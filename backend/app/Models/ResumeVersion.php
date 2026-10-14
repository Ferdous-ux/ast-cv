<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ResumeVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'resume_id',
        'version_number',
        'status',
        'summary',
        'template',
    ];

    protected $casts = [
        'version_number' => 'integer',
    ];

    /**
     * Version belongs to one resume.
     */
    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }

    /**
     * Version has many work experiences.
     */
    public function experiences(): HasMany
    {
        return $this->hasMany(ResumeExperience::class);
    }

    /**
     * Version has many education records.
     */
    public function educations(): HasMany
    {
        return $this->hasMany(ResumeEducation::class);
    }

    /**
     * Version has many projects.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(ResumeProject::class);
    }

    /**
     * Version has many certificates.
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(ResumeCertificate::class);
    }

    /**
     * Version has many awards.
     */
    public function awards(): HasMany
    {
        return $this->hasMany(ResumeAward::class);
    }

    /**
     * Version has many publications.
     */
    public function publications(): HasMany
    {
        return $this->hasMany(ResumePublication::class);
    }

    /**
     * Version has many custom links.
     */
    public function links(): HasMany
    {
        return $this->hasMany(ResumeLink::class);
    }

    /**
     * Version belongs to many skills.
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(
            Skill::class,
            'resume_skills',
            'resume_version_id',
            'skill_id'
        )->withPivot([
            'level',
            'sort_order',
        ])->withTimestamps();
    }

    /**
     * Version belongs to many languages.
     */
    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(
            Language::class,
            'resume_languages',
            'resume_version_id',
            'language_id'
        )->withPivot([
            'proficiency',
            'sort_order',
        ])->withTimestamps();
    }
}