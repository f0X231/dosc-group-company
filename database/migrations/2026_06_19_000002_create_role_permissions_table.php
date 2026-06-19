<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role')->unique();
            $table->json('permissions')->default('[]');
            $table->timestamps();
        });

        $now = now();
        $all = ['dashboard','hero_banner','services','portfolio','blog','packages','partners','testimonials','faq','contacts','settings','users','roles'];

        DB::table('role_permissions')->insert([
            ['role' => 'super_admin', 'permissions' => json_encode($all),                                                          'created_at' => $now, 'updated_at' => $now],
            ['role' => 'admin',       'permissions' => json_encode(['dashboard','hero_banner','services','portfolio','blog','packages','partners','testimonials','faq','contacts','settings']), 'created_at' => $now, 'updated_at' => $now],
            ['role' => 'manager',     'permissions' => json_encode(['dashboard','hero_banner','services','portfolio','blog','packages','partners','testimonials','faq']),                       'created_at' => $now, 'updated_at' => $now],
            ['role' => 'staff',       'permissions' => json_encode(['dashboard','contacts','faq']),                                'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
