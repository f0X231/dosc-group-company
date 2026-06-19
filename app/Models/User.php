<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function getPermissions(): array
    {
        if ($this->role === 'super_admin') {
            return RolePermission::menuKeys();
        }
        return RolePermission::getForRole($this->role);
    }

    public function hasPermission(string $key): bool
    {
        if ($this->role === 'super_admin') return true;
        return in_array($key, $this->getPermissions());
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }
}
