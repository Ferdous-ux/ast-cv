<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
    ];

    /**
     * Skill belongs to many resume versions.
     */
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
}