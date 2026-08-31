<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumeExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'resume_version_id',
        'company_name',
        'job_title',
        'location',
        'start_date',
        'end_date',
        'is_current',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Experience belongs to a resume version.
     */
    public function resumeVersion(): BelongsTo
    {
        return $this->belongsTo(ResumeVersion::class);
    }
}