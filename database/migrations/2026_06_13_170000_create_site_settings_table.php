<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name', 100)->default('DOSC Group');
            $table->string('tagline', 200)->nullable();
            $table->string('logo_path', 500)->nullable();
            $table->string('logo_url', 500)->nullable();
            $table->string('logo_dark_path', 500)->nullable();
            $table->string('logo_dark_url', 500)->nullable();
            $table->string('favicon_path', 500)->nullable();
            $table->string('favicon_url', 500)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('phone_secondary', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('email_secondary', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('google_map_url', 500)->nullable();
            $table->text('google_map_embed')->nullable();
            $table->string('default_og_image_path', 500)->nullable();
            $table->string('default_og_image_url', 500)->nullable();
            $table->string('meta_description', 160)->nullable();
            $table->timestamps();
        });

        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->string('platform', 30);
            $table->string('label', 50);
            $table->string('url', 500)->nullable();
            $table->enum('icon_type', ['builtin', 'emoji', 'image'])->default('builtin');
            $table->string('icon_value', 100)->nullable();
            $table->string('icon_path', 500)->nullable();
            $table->string('icon_url', 500)->nullable();
            $table->char('color', 7)->default('#000000');
            $table->boolean('is_active')->default(false);
            $table->smallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed default site settings
        DB::table('site_settings')->insert([
            'site_name'  => 'DOSC Group',
            'tagline'    => 'ทำเว็บไซต์ให้เป็นเรื่องง่ายสำหรับคุณ',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Pre-seed 7 standard platforms
        $platforms = [
            ['platform' => 'facebook',  'label' => 'Facebook',  'color' => '#1877F2', 'sort_order' => 0],
            ['platform' => 'instagram', 'label' => 'Instagram', 'color' => '#E1306C', 'sort_order' => 1],
            ['platform' => 'line',      'label' => 'LINE',      'color' => '#06C755', 'sort_order' => 2],
            ['platform' => 'youtube',   'label' => 'YouTube',   'color' => '#FF0000', 'sort_order' => 3],
            ['platform' => 'tiktok',    'label' => 'TikTok',    'color' => '#010101', 'sort_order' => 4],
            ['platform' => 'twitter',   'label' => 'X (Twitter)', 'color' => '#000000', 'sort_order' => 5],
            ['platform' => 'linkedin',  'label' => 'LinkedIn',  'color' => '#0A66C2', 'sort_order' => 6],
        ];

        foreach ($platforms as $p) {
            DB::table('social_links')->insert(array_merge($p, [
                'icon_type'  => 'builtin',
                'is_active'  => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('social_links');
        Schema::dropIfExists('site_settings');
    }
};
