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
        Schema::table('chat_rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('chat_rooms', 'author_email')) {
                $table->string('author_email', 191)->nullable()->after('candidate_name');
            }

            if (!Schema::hasColumn('chat_rooms', 'author_name')) {
                $table->string('author_name', 191)->nullable()->after('author_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_rooms', function (Blueprint $table) {
            $table->dropColumn(['author_email', 'author_name']);
        });
    }
};
