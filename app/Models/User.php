<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && in_array($this->role, ['admin', 'manager', 'cashier', 'auditor']);
    }

    public function isAdmin(): bool    { return $this->role === 'admin'; }
    public function isManager(): bool  { return in_array($this->role, ['admin', 'manager']); }
    public function isCashier(): bool  { return in_array($this->role, ['admin', 'manager', 'cashier']); }
    public function isAuditor(): bool  { return $this->role === 'auditor'; }
}
