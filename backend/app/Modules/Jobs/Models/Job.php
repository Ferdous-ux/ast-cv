<?php

namespace App\Modules\Jobs\Models;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Job extends Model
{
    use HasFactory;

    protected $table = 'job_postings';

    protected $fillable = [
        'created_by',
        'title',
        'company_name',
        'location',
        'employment_type',
        'workplace_type',
        'description',
        'requirements',
        'minimum_experience_years',
        'education_level',
        'status',
        'published_at',
    ];

    protected $casts = [
        'created_by' => 'integer',
        'minimum_experience_years' => 'integer',
        'published_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(
            Skill::class,
            'job_skills',
            'job_id',
            'skill_id'
        )->withPivot([
            'importance',
            'weight',
            'minimum_level',
        ])->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}