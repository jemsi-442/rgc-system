<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // JWT IDENTIFIER
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // JWT CUSTOM CLAIMS
    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
        ];
    }
}
