<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Group extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function chats(): HasMany
    {
        return $this->hasMany(GroupChat::class);
    }

    public function chat(): HasOne
    {
        return $this->hasOne(GroupChat::class);
    }

    public function latestChat()
    {
        return $this->chat()->latest();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(GroupAttachment::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }
}
