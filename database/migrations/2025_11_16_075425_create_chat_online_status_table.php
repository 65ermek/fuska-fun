<?php
// database/migrations/2024_01_16_xxxxxx_create_chat_online_status_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatOnlineStatusTable extends Migration
{
    public function up()
    {
        Schema::create('chat_online_status', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique(); // email автора или кандидата
            $table->string('name')->nullable(); // имя для отображения
            $table->timestamp('last_seen');
            $table->boolean('is_online')->default(false);
            $table->string('user_type')->default('candidate'); // author или candidate
            $table->timestamps();

            $table->index('email');
            $table->index('is_online');
            $table->index('user_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_online_status');
    }
}
