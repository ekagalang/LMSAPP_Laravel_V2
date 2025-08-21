<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->enum('grading_mode', ['individual', 'overall'])
                  ->default('individual')
                  ->after('scoring_enabled')
                  ->comment('individual: per question, overall: for entire essay');
        });
    }

    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn('grading_mode');
        });
    }
};