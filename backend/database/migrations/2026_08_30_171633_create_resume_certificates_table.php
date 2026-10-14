<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resume_certificates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('resume_version_id')
                ->constrained('resume_versions')
                ->cascadeOnDelete();

            $table->string('name', 200);

            $table->string('issuer', 200)->nullable();

            $table->string('credential_id', 150)->nullable();

            $table->string('credential_url', 500)->nullable();

            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();

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
        Schema::dropIfExists('resume_certificates');
    }
};