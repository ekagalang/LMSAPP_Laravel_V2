<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('question_answers', function (Blueprint $table) {
            $table->unsignedBigInteger('quiz_attempt_id')->after('id');
            // Kalau perlu foreign key:
            // $table->foreign('quiz_attempt_id')->references('id')->on('quiz_attempts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('question_answers', function (Blueprint $table) {
            $table->dropColumn('quiz_attempt_id');
        });
    }
};
