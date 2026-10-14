<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resume_experiences', function (Blueprint $table) {
            $table->id();

            $table->foreignId('resume_version_id')
                ->constrained('resume_versions')
                ->cascadeOnDelete();

            $table->string('company_name', 150);
            $table->string('job_title', 150);

            $table->string('location', 150)->nullable();

            $table->date('start_date');
            $table->date('end_date')->nullable();

            $table->boolean('is_current')
                ->default(false);

            $table->text('description')->nullable();

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamps();

            $table->index([
                'resume_version_id',
                'sort_order',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_experiences');
    }
};