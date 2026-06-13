<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug', 100)->unique();
            $table->string('tag_text')->nullable();
            $table->string('page_structure')->nullable();
            $table->unsignedInteger('price');
            $table->unsignedInteger('original_price')->nullable();
            $table->boolean('is_vat_excluded')->default(true);
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('color_from', 30)->nullable();
            $table->string('color_to', 30)->nullable();
            $table->string('badge_text', 50)->nullable();
            $table->string('badge_color', 30)->nullable();
            $table->string('cta_primary_text', 100)->nullable();
            $table->string('cta_primary_url')->nullable();
            $table->string('cta_secondary_text', 100)->nullable();
            $table->string('cta_secondary_url')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('detail_url')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
