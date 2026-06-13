<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_media', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('blog_id')->nullable()->nullOnDelete()->constrained('blogs');
            $table->string('blog_uuid', 36)->index();
            $table->string('storage_path', 500);
            $table->string('url', 500);
            $table->string('alt_text', 255)->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->string('mime_type', 50)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_media');
    }
};
