<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle', 200)->nullable();
            $table->text('description')->nullable();
            $table->string('icon_name', 50)->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_url')->nullable();
            $table->string('cta_text', 100)->nullable()->default('ดูรายละเอียด');
            $table->string('cta_url')->nullable();
            $table->string('badge_text', 50)->nullable();
            $table->string('badge_color', 30)->nullable()->default('#7c1d1d');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
