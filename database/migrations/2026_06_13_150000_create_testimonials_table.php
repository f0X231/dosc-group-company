<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name', 100);
            $table->string('customer_title', 150)->nullable();
            $table->string('avatar_path', 500)->nullable();
            $table->string('avatar_url', 500)->nullable();
            $table->char('avatar_color', 7)->default('#1a7a6e');
            $table->text('review_text');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->enum('source', ['direct', 'google', 'facebook'])->default('direct');
            $table->string('source_url', 500)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->smallInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->nullOnDelete()->constrained('users');
            $table->foreignId('updated_by')->nullable()->nullOnDelete()->constrained('users');
            $table->timestamps();
        });

        Schema::create('testimonial_settings', function (Blueprint $table) {
            $table->id();
            $table->string('label', 50)->default('TESTIMONIALS');
            $table->string('heading', 200)->default('กำลังใจสำคัญของเรา');
            $table->char('bg_color', 7)->default('#7c1d1d');
            $table->string('featured_image_path', 500)->nullable();
            $table->string('featured_image_url', 500)->nullable();
            $table->string('overlay_text', 150)->nullable();
            $table->string('cta_text', 50)->default('ดูเพิ่มเติม');
            $table->string('cta_url', 500)->nullable();
            $table->timestamps();
        });

        // Seed one default settings row
        DB::table('testimonial_settings')->insert([
            'label'        => 'TESTIMONIALS',
            'heading'      => 'กำลังใจสำคัญของเรา',
            'bg_color'     => '#7c1d1d',
            'overlay_text' => 'ขอบคุณทุกกำลังใจที่ให้กับทีมเรา',
            'cta_text'     => 'ดูเพิ่มเติม',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonial_settings');
        Schema::dropIfExists('testimonials');
    }
};
