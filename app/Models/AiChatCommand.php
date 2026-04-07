<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiChatCommand extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message',
        'parsed_action',
        'parsed_payload',
        'status',
        'result_message',
        'requires_confirmation',
        'confirmed_at',
        'executed_at',
    ];

    protected $casts = [
        'parsed_payload' => 'array',
        'requires_confirmation' => 'boolean',
        'confirmed_at' => 'datetime',
        'executed_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_PARSED = 'parsed';
    public const STATUS_EXECUTED = 'executed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_BLOCKED = 'blocked';
    public const STATUS_CONFIRMED = 'confirmed';

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PARSED,
            self::STATUS_EXECUTED,
            self::STATUS_FAILED,
            self::STATUS_BLOCKED,
            self::STATUS_CONFIRMED,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeExecuted($query)
    {
        return $query->where('status', self::STATUS_EXECUTED);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeRequiresConfirmation($query)
    {
        return $query->where('requires_confirmation', true);
    }

    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCKED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isExecuted(): bool
    {
        return $this->status === self::STATUS_EXECUTED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function requiresConfirmation(): bool
    {
        return $this->requires_confirmation === true && !$this->confirmed_at;
    }
}
