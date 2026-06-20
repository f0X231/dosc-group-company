<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $fillable = ['role', 'label', 'permissions', 'is_system'];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'is_system'   => 'boolean',
        ];
    }

    public static function menuKeys(): array
    {
        return [
            'dashboard', 'hero_banner', 'services', 'portfolio', 'blog',
            'packages', 'partners', 'testimonials', 'faq', 'contacts',
            'settings', 'seo', 'users', 'roles',
        ];
    }

    public static function menuLabels(): array
    {
        return [
            'dashboard'    => 'Dashboard',
            'hero_banner'  => 'Hero Banner',
            'services'     => 'บริการของเรา',
            'portfolio'    => 'จัดการผลงาน',
            'blog'         => 'จัดการบทความ',
            'packages'     => 'จัดการแพ็กเกจ',
            'partners'     => 'พาร์ทเนอร์ / ลูกค้า',
            'testimonials' => 'รีวิวจากลูกค้า',
            'faq'          => 'จัดการ FAQ',
            'contacts'     => 'ข้อความติดต่อ',
            'settings'     => 'ตั้งค่าเว็บไซต์',
            'seo'          => 'จัดการ SEO',
            'users'        => 'จัดการผู้ใช้',
            'roles'        => 'จัดการสิทธิ์',
        ];
    }

    public static function menuGroups(): array
    {
        return [
            ['label' => 'เนื้อหาหน้าเว็บ', 'keys' => ['hero_banner', 'services', 'portfolio', 'blog', 'packages', 'partners', 'testimonials', 'faq']],
            ['label' => 'จัดการระบบ',       'keys' => ['contacts', 'settings', 'seo']],
            ['label' => 'ผู้ใช้และสิทธิ์',   'keys' => ['users', 'roles']],
        ];
    }

    public static function getForRole(string $role): array
    {
        return static::where('role', $role)->value('permissions') ?? [];
    }

    public static function allRoles(): \Illuminate\Support\Collection
    {
        return static::orderBy('is_system', 'desc')->orderBy('id')->get();
    }

    public static function slugify(string $label): string
    {
        return preg_replace('/[^a-z0-9]+/', '_', strtolower(trim($label)));
    }
}
