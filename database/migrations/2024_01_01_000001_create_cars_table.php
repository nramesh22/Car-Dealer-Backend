<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('brand');
            $table->string('model');
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('price');
            $table->unsignedInteger('mileage');
            $table->string('fuel_type');
            $table->string('transmission');
            $table->text('description');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 255)->nullable();
            $table->string('slug')->unique();
            $table->string('featured_image_path')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_path')->nullable();
            $table->timestamps();

            $table->index(['status', 'brand', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
