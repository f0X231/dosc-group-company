<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('logo_path', 500)->nullable();
            $table->string('logo_url', 500)->nullable();
            $table->string('website_url', 500)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->smallInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->nullOnDelete()->constrained('users');
            $table->foreignId('updated_by')->nullable()->nullOnDelete()->constrained('users');
            $table->timestamps();
        });

        Schema::create('partner_settings', function (Blueprint $table) {
            $table->id();
            $table->string('heading', 200)->default('ลูกค้าที่ให้ความไว้วางใจกับเรา');
            $table->char('bg_color', 7)->default('#7c1d1d');
            $table->unsignedTinyInteger('logos_per_row')->default(4);
            $table->boolean('show_name')->default(false);
            $table->boolean('grayscale')->default(true);
            $table->timestamps();
        });

        DB::table('partner_settings')->insert([
            'heading'       => 'ลูกค้าที่ให้ความไว้วางใจกับเรา',
            'bg_color'      => '#7c1d1d',
            'logos_per_row' => 4,
            'show_name'     => false,
            'grayscale'     => true,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_settings');
        Schema::dropIfExists('partners');
    }
};
