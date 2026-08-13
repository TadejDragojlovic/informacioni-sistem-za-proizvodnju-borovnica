<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function narudzbine(): HasMany
    {
        return $this->hasMany(Narudzbina::class, 'user_id');
    }

    public function lotDogadjaji(): HasMany
    {
        return $this->hasMany(LotDogadjaj::class, 'evidentirao_user_id');
    }

    public function resursi(): HasMany
    {
        return $this->hasMany(Resurs::class, 'evidentirao_user_id');
    }
}
