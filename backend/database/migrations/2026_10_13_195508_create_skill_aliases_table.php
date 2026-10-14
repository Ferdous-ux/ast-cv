<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skill_aliases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('skill_id')
                ->constrained('skills')
                ->cascadeOnDelete();

            $table->string('alias', 150);

            $table->string('normalized_alias', 150);

            $table->string('language', 10)
                ->nullable();

            $table->timestamps();

            $table->unique([
                'skill_id',
                'normalized_alias',
            ]);

            $table->index('normalized_alias');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skill_aliases');
    }
};