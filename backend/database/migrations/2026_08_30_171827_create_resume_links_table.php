<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resume_links', function (Blueprint $table) {
            $table->id();

            $table->foreignId('resume_version_id')
                ->constrained('resume_versions')
                ->cascadeOnDelete();

            $table->string('label', 100);

            $table->string('url', 500);

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
        Schema::dropIfExists('resume_links');
    }
};