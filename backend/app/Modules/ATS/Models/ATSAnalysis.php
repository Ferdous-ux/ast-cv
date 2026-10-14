<?php

namespace App\Modules\ATS\Models;

use App\Models\ResumeVersion;
use App\Models\User;
use App\Modules\Jobs\Models\Job;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ATSAnalysis extends Model
{
    use HasFactory;

    protected $table = 'ats_analyses';

    protected $fillable = [
        'resume_version_id',
        'job_posting_id',
        'analyzed_by',
        'overall_score',
        'skills_score',
        'experience_score',
        'education_score',
        'keywords_score',
        'structure_score',
        'matched_skills',
        'missing_skills',
        'matched_keywords',
        'missing_keywords',
        'recommendations',
        'details',
        'status',
        'analyzed_at',
    ];

    protected $casts = [
        'resume_version_id' => 'integer',
        'job_posting_id' => 'integer',
        'analyzed_by' => 'integer',
        'overall_score' => 'float',
        'skills_score' => 'float',
        'experience_score' => 'float',
        'education_score' => 'float',
        'keywords_score' => 'float',
        'structure_score' => 'float',
        'matched_skills' => 'array',
        'missing_skills' => 'array',
        'matched_keywords' => 'array',
        'missing_keywords' => 'array',
        'recommendations' => 'array',
        'details' => 'array',
        'analyzed_at' => 'datetime',
    ];

    public function resumeVersion(): BelongsTo
    {
        return $this->belongsTo(ResumeVersion::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(
            Job::class,
            'job_posting_id'
        );
    }

    public function analyzer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'analyzed_by'
        );
    }
}