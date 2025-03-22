<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'number',
        'email',
        'password',
        'token',
        'profile_picture',
        'description',
        'type',
        'user_type',
    ];

    protected $hidden = [
        'password',
        'token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];
}
