<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('certificates', function (Blueprint $table) {
            // PERBAIKAN: Mengubah 'certificate_number' menjadi 'certificate_code'
            $table->string('place_of_birth')->nullable()->after('certificate_code');
            $table->date('date_of_birth')->nullable()->after('place_of_birth');
            $table->string('identity_number')->nullable()->after('date_of_birth');
            $table->string('institution_name')->nullable()->after('identity_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn(['place_of_birth', 'date_of_birth', 'identity_number', 'institution_name']);
        });
    }
};
