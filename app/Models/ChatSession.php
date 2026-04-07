<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'type',
        'context',
        'is_starred',
        'last_message_at',
    ];

    protected $casts = [
        'is_starred' => 'boolean',
        'last_message_at' => 'datetime',
    ];

    public const TYPE_USER = 'user';
    public const TYPE_ADMIN = 'admin';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at', 'asc');
    }

    public function latestMessage(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->latest()->limit(1);
    }

    public function scopeUserSessions($query, $userId)
    {
        return $query->where('user_id', $userId)->where('type', self::TYPE_USER);
    }

    public function scopeAdminSessions($query)
    {
        return $query->where('type', self::TYPE_ADMIN);
    }

    public function scopeStarred($query)
    {
        return $query->where('is_starred', true);
    }

    public function scopeRecent($query)
    {
        return $query->orderByDesc('last_message_at');
    }
}
