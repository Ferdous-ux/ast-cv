<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skill extends Model
{
    use HasFactory;

    public const ORIGIN_CATALOG = 'catalog';
    public const ORIGIN_USER = 'user';

    public const STATUS_APPROVED = 'approved';
    public const STATUS_PENDING = 'pending';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'name',
        'category',
        'origin',
        'approval_status',
        'created_by',
    ];

    protected $casts = [
        'created_by' => 'integer',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function scopeApproved($query)
    {
        return $query->where(
            'approval_status',
            self::STATUS_APPROVED
        );
    }

    public function getIsCustomAttribute(): bool
    {
        return $this->origin === self::ORIGIN_USER;
    }
}