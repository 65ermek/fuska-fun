<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('job_photos', function (Blueprint $t) {
            $t->id();
            $t->foreignId('job_id')->constrained()->cascadeOnDelete();
            $t->string('path');                 // storage path, напр. "public/jobs/abcd.jpg"
            $t->unsignedSmallInteger('sort')->default(0);
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('job_photos'); }
};
