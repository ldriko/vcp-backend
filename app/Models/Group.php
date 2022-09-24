<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function chats(): HasMany
    {
        return $this->hasMany(GroupChat::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(GroupChatAttachment::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }
}
