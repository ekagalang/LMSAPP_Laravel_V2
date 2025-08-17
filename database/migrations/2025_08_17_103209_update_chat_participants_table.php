<?php
// Ganti isi file: database/migrations/2025_08_17_103209_update_chat_participants_table.php
// dengan kode di bawah ini

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
        Schema::table('chat_participants', function (Blueprint $table) {
            // Cek dulu sebelum add kolom - tidak akan error jika sudah ada

            if (!Schema::hasColumn('chat_participants', 'last_read_at')) {
                $table->timestamp('last_read_at')->nullable()->after('joined_at');
            }

            if (!Schema::hasColumn('chat_participants', 'status')) {
                $table->enum('status', ['active', 'inactive', 'left'])->default('active')->after('last_read_at');
            }

            if (!Schema::hasColumn('chat_participants', 'notifications_enabled')) {
                $table->boolean('notifications_enabled')->default(true)->after('status');
            }
        });

        // Add indexes - akan skip jika error
        try {
            Schema::table('chat_participants', function (Blueprint $table) {
                $table->index(['chat_id', 'user_id', 'status']);
            });
        } catch (\Exception $e) {
            // Skip jika index sudah ada
        }

        try {
            Schema::table('chat_participants', function (Blueprint $table) {
                $table->index(['user_id', 'last_read_at']);
            });
        } catch (\Exception $e) {
            // Skip jika index sudah ada
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_participants', function (Blueprint $table) {
            // Drop indexes first
            try {
                $table->dropIndex(['chat_id', 'user_id', 'status']);
            } catch (\Exception $e) {
                // Skip jika index tidak ada
            }

            try {
                $table->dropIndex(['user_id', 'last_read_at']);
            } catch (\Exception $e) {
                // Skip jika index tidak ada
            }

            // Drop kolom yang baru ditambah (jangan drop last_read_at karena mungkin sudah ada)
            $columnsToDrop = [];

            if (Schema::hasColumn('chat_participants', 'notifications_enabled')) {
                $columnsToDrop[] = 'notifications_enabled';
            }

            if (Schema::hasColumn('chat_participants', 'status')) {
                $columnsToDrop[] = 'status';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
