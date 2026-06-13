<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Content
            $table->string('title', 300);
            $table->string('slug', 350)->unique();
            $table->string('excerpt', 500)->nullable();
            $table->longText('content')->nullable();
            $table->unsignedTinyInteger('reading_time')->default(1);

            // Media
            $table->string('cover_image_path', 500)->nullable();
            $table->string('cover_image_url', 500)->nullable();
            $table->string('thumbnail_path', 500)->nullable();
            $table->string('thumbnail_url', 500)->nullable();
            $table->string('og_image_url', 500)->nullable();

            // Category & Tags
            $table->foreignId('category_id')->nullable()->nullOnDelete()->constrained('blog_categories');
            $table->jsonb('tags')->nullable();

            // Publishing
            $table->enum('status', ['draft', 'scheduled', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedBigInteger('view_count')->default(0);

            // SEO
            $table->string('meta_title', 70)->nullable();
            $table->string('meta_description', 160)->nullable();
            $table->string('meta_keywords', 255)->nullable();
            $table->string('canonical_url', 500)->nullable();

            // Scripts
            $table->text('head_script')->nullable();
            $table->text('body_script')->nullable();

            // Meta
            $table->foreignId('created_by')->nullable()->nullOnDelete()->constrained('users');
            $table->foreignId('updated_by')->nullable()->nullOnDelete()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
