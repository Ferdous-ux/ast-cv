<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_skills', function (Blueprint $table) {
            $table->id();

            $table->foreignId('job_id')
                ->constrained('job_postings')
                ->cascadeOnDelete();

            $table->foreignId('skill_id')
                ->constrained('skills')
                ->restrictOnDelete();

            $table->string('importance', 30)
                ->default('required');

            $table->unsignedTinyInteger('weight')
                ->default(100);

            $table->string('minimum_level', 30)
                ->nullable();

            $table->timestamps();

            $table->unique([
                'job_id',
                'skill_id',
            ]);

            $table->index([
                'job_id',
                'importance',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_skills');
    }
};