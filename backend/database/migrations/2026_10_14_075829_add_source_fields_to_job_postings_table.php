<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->string('source_type', 30)
                ->default('platform')
                ->index()
                ->after('created_by');

            $table->string('source_url', 2048)
                ->nullable()
                ->after('source_type');

            $table->index([
                'source_type',
                'created_by',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropIndex([
                'source_type',
                'created_by',
            ]);

            $table->dropColumn([
                'source_type',
                'source_url',
            ]);
        });
    }
};