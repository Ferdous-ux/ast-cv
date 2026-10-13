<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('title', 150);

            $table->string('company_name', 150)
                ->nullable();

            $table->string('location', 150)
                ->nullable();

            $table->string('employment_type', 30)
                ->nullable()
                ->index();

            $table->string('workplace_type', 30)
                ->nullable()
                ->index();

            $table->longText('description');

            $table->longText('requirements')
                ->nullable();

            $table->unsignedTinyInteger('minimum_experience_years')
                ->nullable();

            $table->string('education_level', 100)
                ->nullable();

            $table->string('status', 30)
                ->default('draft')
                ->index();

            $table->timestamp('published_at')
                ->nullable();

            $table->timestamps();

            $table->index('title');
            $table->index('company_name');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};