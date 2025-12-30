<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('jobs', function (Blueprint $t) {
            $t->id();

            $t->foreignId('job_category_id')->constrained()->cascadeOnDelete();

            $t->string('city')->index();
            $t->string('district')->nullable();

            $t->string('title', 120);
            $t->text('description');

            $t->enum('pay_type', ['per_hour','per_job'])->default('per_job');
            $t->unsignedInteger('price')->nullable();
            $t->boolean('price_negotiable')->default(false);

            $t->string('contact_name')->nullable();
            $t->string('phone', 40)->nullable();
            $t->string('telegram', 60)->nullable();
            $t->string('whatsapp', 40)->nullable();

            $t->enum('status', ['published','pending','hidden'])->default('published')->index();
            $t->string('edit_token', 64)->unique();

            $t->string('lang', 8)->default('cs');
            $t->string('ip', 45)->nullable();
            $t->string('ua', 255)->nullable();

            $t->timestamps();
            $t->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('jobs'); }
};
