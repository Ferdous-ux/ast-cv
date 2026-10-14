<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ats_analyses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('resume_version_id')
                ->constrained('resume_versions')
                ->cascadeOnDelete();

            $table->foreignId('job_posting_id')
                ->constrained('job_postings')
                ->cascadeOnDelete();

            $table->foreignId('analyzed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->decimal('overall_score', 5, 2)
                ->default(0);

            $table->decimal('skills_score', 5, 2)
                ->default(0);

            $table->decimal('experience_score', 5, 2)
                ->default(0);

            $table->decimal('education_score', 5, 2)
                ->default(0);

            $table->decimal('keywords_score', 5, 2)
                ->default(0);

            $table->decimal('structure_score', 5, 2)
                ->default(0);

            $table->json('matched_skills')
                ->nullable();

            $table->json('missing_skills')
                ->nullable();

            $table->json('matched_keywords')
                ->nullable();

            $table->json('missing_keywords')
                ->nullable();

            $table->json('recommendations')
                ->nullable();

            $table->json('details')
                ->nullable();

            $table->string('status', 30)
                ->default('completed')
                ->index();

            $table->timestamp('analyzed_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'resume_version_id',
                'job_posting_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ats_analyses');
    }
};