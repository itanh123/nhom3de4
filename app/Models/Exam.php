<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'created_by',
        'title',
        'description',
        'duration_mins',
        'pass_score',
        'shuffle_q',
        'shuffle_a',
        'show_explain',
        'is_active',
        'start_time',
        'end_time',
        'status',
        'is_published',
    ];

    protected $casts = [
        'duration_mins' => 'integer',
        'pass_score' => 'integer',
        'shuffle_q' => 'boolean',
        'shuffle_a' => 'boolean',
        'show_explain' => 'boolean',
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_ARCHIVED = 'archived';

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_SCHEDULED,
            self::STATUS_OPEN,
            self::STATUS_CLOSED,
            self::STATUS_ARCHIVED,
        ];
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function examQuestions(): HasMany
    {
        return $this->hasMany(ExamQuestion::class);
    }

    public function examResults(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }

    public function results(): HasMany
    {
        return $this->examResults();
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function canTake(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if (! $this->is_published && $this->created_by !== auth()->id()) {
            return false;
        }

        if (in_array($this->status, [self::STATUS_CLOSED, self::STATUS_ARCHIVED], true)) {
            return false;
        }

        if ($this->start_time && now()->lt($this->start_time)) {
            return false;
        }

        if ($this->end_time && now()->gt($this->end_time)) {
            return false;
        }

        return true;
    }
}
