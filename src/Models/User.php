<?php

namespace Jhoff\EloquentExtraAttributes\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Jhoff\EloquentExtraAttributes\Traits\ExtraAttributes;

class User extends Authenticatable
{
    use ExtraAttributes;
    use HasFactory;

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $allowedExtras = [];

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
