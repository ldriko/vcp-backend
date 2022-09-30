<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = [];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function groups(): HasManyThrough
    {
        return $this->hasManyThrough(Group::class, GroupMember::class, 'user_id', 'id');
    }

    public function journals(): HasMany
    {
        return $this->hasMany(Journal::class);
    }
}
