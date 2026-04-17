<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->float('discount')->default(0);
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->float('rate')->default(0);
            $table->json('course_includes')->nullable();
            $table->string('video_img_link')->nullable();
            $table->string('video_link')->nullable();
            $table->string('img_link')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
