<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('languages', function (Blueprint $table) {
            $table->string('code', 10)
                ->unique()
                ->after('id');

            $table->string('native_name', 100)
                ->nullable()
                ->after('name');

            $table->string('direction', 3)
                ->default('ltr')
                ->after('native_name');

            $table->boolean('is_active')
                ->default(true)
                ->after('direction')
                ->index();
        });

        Schema::table('resumes', function (Blueprint $table) {
            $table->foreignId('language_id')
                ->nullable()
                ->after('user_id')
                ->constrained('languages')
                ->restrictOnDelete();

            $table->index('language_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            $table->dropForeign(['language_id']);
            $table->dropIndex(['language_id']);
            $table->dropColumn('language_id');
        });

        Schema::table('languages', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropUnique(['code']);
            $table->dropColumn([
                'code',
                'native_name',
                'direction',
                'is_active',
            ]);
        });
    }
};