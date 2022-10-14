<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Journal extends Model
{
    use HasFactory;

    protected $primaryKey = 'code';
    protected $keyType = 'string';
    protected $guarded = [];
    protected $casts = [
        'code' => 'string',
        'is_published' => 'boolean'
    ];

    public $incrementing = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): HasOneThrough
    {
        return $this->hasOneThrough(Category::class, JournalCategory::class, 'journal_code', 'id', 'category_id');
    }

    public function categoriesTunnel(): HasOne
    {
        return $this->hasOne(JournalCategory::class);
    }
}
