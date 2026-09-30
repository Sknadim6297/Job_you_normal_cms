<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_postings', function (Blueprint $table): void {
            $table->index('updated_at');
            $table->index(['is_published', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table): void {
            $table->dropIndex('job_postings_updated_at_index');
            $table->dropIndex('job_postings_is_published_published_at_index');
        });
    }
};