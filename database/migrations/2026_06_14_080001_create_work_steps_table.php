<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('step_number');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon_name', 50)->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_url')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_steps');
    }
};
