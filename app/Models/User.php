<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\Authenticatable;

class User extends Model implements Authenticatable,JWTSubject
{
    use HasFactory;
    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
    ];

    public function orders(){
        return $this->hasMany(Order::class);
    }

    public function reviews(){
        return $this->hasMany(Reviews::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value)
    {
        // No action needed
    }

    public function getRememberTokenName()
    {
        return null;
    }
}