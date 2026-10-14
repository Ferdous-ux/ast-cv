<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resume_skills', function (Blueprint $table) {
            $table->id();

            $table->foreignId('resume_version_id')
                ->constrained('resume_versions')
                ->cascadeOnDelete();

            $table->foreignId('skill_id')
                ->constrained('skills')
                ->restrictOnDelete();

            $table->string('level', 30)->nullable();

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamps();

            $table->unique([
                'resume_version_id',
                'skill_id',
            ]);

            $table->index([
                'resume_version_id',
                'sort_order',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_skills');
    }
};