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
        Schema::table('course_classes', function (Blueprint $table) {
            $table->datetime('start_date')->nullable()->change();
            $table->datetime('end_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_classes', function (Blueprint $table) {
            $table->datetime('start_date')->nullable(false)->change();
            $table->datetime('end_date')->nullable(false)->change();
        });
    }
};
