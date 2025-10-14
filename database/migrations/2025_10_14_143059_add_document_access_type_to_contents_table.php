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
        Schema::table('contents', function (Blueprint $table) {
            // Menambahkan field untuk kontrol akses dokumen
            // Options: 'both' (default), 'download_only', 'preview_only'
            $table->enum('document_access_type', ['both', 'download_only', 'preview_only'])
                  ->default('both')
                  ->after('file_path')
                  ->comment('Kontrol akses untuk dokumen: both=preview & download, download_only=hanya download, preview_only=hanya preview');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn('document_access_type');
        });
    }
};
