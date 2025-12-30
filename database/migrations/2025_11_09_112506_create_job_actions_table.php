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
        if (!Schema::hasTable('job_actions')) {
            Schema::create('job_actions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('job_id');
                $table->string('email')->nullable();
                $table->string('action');
                $table->string('ip')->nullable();
                $table->text('meta')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_actions');
    }
};
