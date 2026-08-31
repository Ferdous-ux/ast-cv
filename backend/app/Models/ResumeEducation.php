<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumeEducation extends Model
{
    use HasFactory;

    protected $fillable = [
        'resume_version_id',
        'institution',
        'degree',
        'field_of_study',
        'location',
        'start_date',
        'end_date',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'sort_order' => 'integer',
    ];

    /**
     * Education belongs to a resume version.
     */
    public function resumeVersion(): BelongsTo
    {
        return $this->belongsTo(ResumeVersion::class);
    }
}