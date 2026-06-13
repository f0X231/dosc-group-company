<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_banners', function (Blueprint $table) {
            $table->id();

            // Content
            $table->string('badge_text', 100)->nullable();
            $table->string('headline', 300)->default('');
            $table->string('headline_highlight', 150)->nullable();
            $table->text('subtext')->nullable();
            $table->string('cta_text', 100)->default('ปรึกษาฟรี');
            $table->string('cta_url', 500)->nullable();
            $table->jsonb('stats')->nullable();

            // Media
            $table->enum('media_type', ['none', 'video', 'image_full', 'image_side'])->default('none');
            $table->string('media_path', 500)->nullable();
            $table->string('media_url', 500)->nullable();

            // Overlay (video / image_full only)
            $table->unsignedTinyInteger('overlay_opacity')->default(40);
            $table->string('overlay_color', 7)->default('#000000');

            // Positioning (image_side only)
            $table->enum('image_position', ['right', 'left'])->default('right');
            $table->enum('image_size', ['sm', 'md', 'lg'])->default('md');
            $table->enum('image_align_y', ['top', 'center', 'bottom'])->default('center');

            // Background
            $table->enum('bg_type', ['gradient', 'solid'])->default('gradient');
            $table->string('bg_color_from', 7)->default('#f1f1f1');
            $table->string('bg_color_to', 7)->default('#e5e5e5');

            // Style
            $table->enum('text_color', ['dark', 'light'])->default('dark');
            $table->enum('animation_style', ['slide_up', 'typewriter', 'fade'])->default('slide_up');

            // Meta
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->nullOnDelete()->constrained('users');
            $table->foreignId('updated_by')->nullable()->nullOnDelete()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_banners');
    }
};
