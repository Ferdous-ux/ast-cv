<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->string('origin', 20)
                ->default('catalog')
                ->after('category')
                ->index();

            $table->string('approval_status', 20)
                ->default('approved')
                ->after('origin')
                ->index();

            $table->foreignId('created_by')
                ->nullable()
                ->after('approval_status')
                ->constrained('users')
                ->nullOnDelete();

            $table->index([
                'created_by',
                'approval_status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->dropForeign([
                'created_by',
            ]);

            $table->dropIndex([
                'created_by',
                'approval_status',
            ]);

            $table->dropColumn([
                'origin',
                'approval_status',
                'created_by',
            ]);
        });
    }
};