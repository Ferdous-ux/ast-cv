<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resume_versions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('resume_id')
                ->constrained('resumes')
                ->cascadeOnDelete();

            $table->unsignedInteger('version_number');

            $table->string('status', 30)
                ->default('draft')
                ->index();

            $table->text('summary')->nullable();

            $table->string('template', 50)
                ->default('default');

            $table->timestamps();

            $table->unique([
                'resume_id',
                'version_number',
            ]);

            $table->index('resume_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_versions');
    }
};