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
        Schema::create('top_payments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('job_id');
            $table->unsignedTinyInteger('count');
            $table->unsignedInteger('amount');
            $table->char('code', 6)->unique();
            $table->enum('status', ['pending', 'paid'])->default('pending');

            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            // если есть таблица jobs
            $table->foreign('job_id')
                ->references('id')
                ->on('jobs')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('top_payments');
    }
};
