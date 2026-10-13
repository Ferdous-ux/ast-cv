<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
    ];

    public function resumeVersions(): BelongsToMany
    {
        return $this->belongsToMany(
            ResumeVersion::class,
            'resume_skills',
            'skill_id',
            'resume_version_id'
        )->withPivot([
            'level',
            'sort_order',
        ])->withTimestamps();
    }

    public function aliases(): HasMany
    {
        return $this->hasMany(
            SkillAlias::class,
            'skill_id'
        );
    }
}