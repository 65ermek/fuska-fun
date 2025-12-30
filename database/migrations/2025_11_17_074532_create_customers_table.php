<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->string('email');
                $table->string('phone')->nullable();
                $table->string('name');
                $table->string('company')->nullable();
                $table->string('persistent_token');
                $table->timestamp('last_seen_at')->nullable();
                $table->string('source')->default('chat');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
