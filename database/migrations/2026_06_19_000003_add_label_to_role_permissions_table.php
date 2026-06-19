<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('role_permissions', function (Blueprint $table) {
            $table->string('label')->after('role')->default('');
            $table->boolean('is_system')->default(false)->after('permissions');
        });

        DB::table('role_permissions')->where('role', 'super_admin')->update(['label' => 'Super Admin', 'is_system' => true]);
        DB::table('role_permissions')->where('role', 'admin')->update(['label' => 'Admin']);
        DB::table('role_permissions')->where('role', 'manager')->update(['label' => 'Manager']);
        DB::table('role_permissions')->where('role', 'staff')->update(['label' => 'Staff']);
    }

    public function down(): void
    {
        Schema::table('role_permissions', function (Blueprint $table) {
            $table->dropColumn(['label', 'is_system']);
        });
    }
};
