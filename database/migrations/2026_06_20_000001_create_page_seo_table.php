<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_seo', function (Blueprint $table) {
            $table->id();
            $table->string('page_key')->unique();
            $table->string('page_label');
            $table->string('meta_title', 100)->nullable();
            $table->string('meta_description', 300)->nullable();
            $table->string('og_title', 100)->nullable();
            $table->string('og_description', 300)->nullable();
            $table->string('og_image_path')->nullable();
            $table->string('og_image_url')->nullable();
            $table->string('robots', 50)->default('index,follow');
            $table->string('canonical_url', 500)->nullable();
            $table->text('schema_json')->nullable();
            $table->timestamps();
        });

        DB::table('page_seo')->insert([
            ['page_key' => 'home',                'page_label' => 'หน้าแรก',                'robots' => 'index,follow',   'created_at' => now(), 'updated_at' => now()],
            ['page_key' => 'portfolio',           'page_label' => 'ผลงานทั้งหมด',           'robots' => 'index,follow',   'created_at' => now(), 'updated_at' => now()],
            ['page_key' => 'services.index',      'page_label' => 'บริการทั้งหมด',          'robots' => 'index,follow',   'created_at' => now(), 'updated_at' => now()],
            ['page_key' => 'services.website',    'page_label' => 'รับทำเว็บไซต์',         'robots' => 'index,follow',   'created_at' => now(), 'updated_at' => now()],
            ['page_key' => 'services.google-ads', 'page_label' => 'รับทำ Google Ads',       'robots' => 'index,follow',   'created_at' => now(), 'updated_at' => now()],
            ['page_key' => 'faq',                 'page_label' => 'คำถามที่พบบ่อย',         'robots' => 'index,follow',   'created_at' => now(), 'updated_at' => now()],
            ['page_key' => 'blog.index',          'page_label' => 'บทความ',                 'robots' => 'index,follow',   'created_at' => now(), 'updated_at' => now()],
            ['page_key' => 'contact',             'page_label' => 'ติดต่อเรา',              'robots' => 'index,follow',   'created_at' => now(), 'updated_at' => now()],
            ['page_key' => 'privacy-policy',      'page_label' => 'นโยบายความเป็นส่วนตัว', 'robots' => 'noindex,follow', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('page_seo');
    }
};
