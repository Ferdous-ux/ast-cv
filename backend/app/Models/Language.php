<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

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
}