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
            Schema::table('certificate_templates', function (Blueprint $table) {
                // Hapus kolom lama karena path gambar akan disimpan di dalam JSON layout_data
                $table->dropColumn('background_image_path');
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('certificate_templates', function (Blueprint $table) {
                // Jika migrasi di-rollback, tambahkan kembali kolomnya
                $table->string('background_image_path')->after('name');
            });
        }
    };
    