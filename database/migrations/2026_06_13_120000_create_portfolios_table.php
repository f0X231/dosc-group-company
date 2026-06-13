<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('package_id')->nullable()->nullOnDelete()->constrained('packages');
            $table->string('package_name', 100)->nullable();
            $table->string('client_url', 500)->nullable();
            $table->string('video_path', 500)->nullable();
            $table->string('video_url', 500)->nullable();
            $table->string('thumbnail_path', 500)->nullable();
            $table->string('thumbnail_url', 500)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->nullOnDelete()->constrained('users');
            $table->foreignId('updated_by')->nullable()->nullOnDelete()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
