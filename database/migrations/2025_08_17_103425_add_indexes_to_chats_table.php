<?php
// Ganti isi file: database/migrations/2025_08_17_103425_add_indexes_to_chats_table.php
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
        // Add indexes to chats table
        if (Schema::hasTable('chats')) {
            try {
                Schema::table('chats', function (Blueprint $table) {
                    $table->index(['type', 'updated_at']);
                });
            } catch (\Exception $e) {
                // Skip jika index sudah ada
            }

            try {
                Schema::table('chats', function (Blueprint $table) {
                    $table->index(['course_period_id', 'type']);
                });
            } catch (\Exception $e) {
                // Skip jika index sudah ada
            }

            try {
                Schema::table('chats', function (Blueprint $table) {
                    $table->index(['created_by']);
                });
            } catch (\Exception $e) {
                // Skip jika index sudah ada
            }
        }

        // Add indexes to messages table
        if (Schema::hasTable('messages')) {
            try {
                Schema::table('messages', function (Blueprint $table) {
                    $table->index(['chat_id', 'created_at']);
                });
            } catch (\Exception $e) {
                // Skip jika index sudah ada
            }

            try {
                Schema::table('messages', function (Blueprint $table) {
                    $table->index(['user_id', 'created_at']);
                });
            } catch (\Exception $e) {
                // Skip jika index sudah ada
            }

            try {
                Schema::table('messages', function (Blueprint $table) {
                    $table->index(['chat_id', 'user_id', 'created_at']);
                });
            } catch (\Exception $e) {
                // Skip jika index sudah ada
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes from chats table
        if (Schema::hasTable('chats')) {
            try {
                Schema::table('chats', function (Blueprint $table) {
                    $table->dropIndex(['type', 'updated_at']);
                });
            } catch (\Exception $e) {
                // Skip jika index tidak ada
            }

            try {
                Schema::table('chats', function (Blueprint $table) {
                    $table->dropIndex(['course_period_id', 'type']);
                });
            } catch (\Exception $e) {
                // Skip jika index tidak ada
            }

            try {
                Schema::table('chats', function (Blueprint $table) {
                    $table->dropIndex(['created_by']);
                });
            } catch (\Exception $e) {
                // Skip jika index tidak ada
            }
        }

        // Drop indexes from messages table
        if (Schema::hasTable('messages')) {
            try {
                Schema::table('messages', function (Blueprint $table) {
                    $table->dropIndex(['chat_id', 'created_at']);
                });
            } catch (\Exception $e) {
                // Skip jika index tidak ada
            }

            try {
                Schema::table('messages', function (Blueprint $table) {
                    $table->dropIndex(['user_id', 'created_at']);
                });
            } catch (\Exception $e) {
                // Skip jika index tidak ada
            }

            try {
                Schema::table('messages', function (Blueprint $table) {
                    $table->dropIndex(['chat_id', 'user_id', 'created_at']);
                });
            } catch (\Exception $e) {
                // Skip jika index tidak ada
            }
        }
    }
};
