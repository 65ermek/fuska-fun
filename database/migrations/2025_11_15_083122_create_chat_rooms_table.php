<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatRoomsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('chat_rooms')) {
            Schema::create('chat_rooms', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('job_id');
                $table->string('candidate_email');
                $table->string('candidate_name', 100);
                $table->string('author_token')->nullable();
                $table->string('candidate_token')->nullable();
                $table->enum('status', ['active', 'closed', 'archived'])->default('active');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('chat_rooms');
    }
}
