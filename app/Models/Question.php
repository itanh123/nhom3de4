<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    public const TYPE_SINGLE_CHOICE = 'single_choice';
    public const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    public const TYPE_FILL_IN_BLANK = 'fill_in_blank';

    public const DIFFICULTY_EASY = 'easy';
    public const DIFFICULTY_MEDIUM = 'medium';
    public const DIFFICULTY_HARD = 'hard';

    protected $fillable = [
        'topic_id',
        'created_by',
        'type',
        'difficulty',
        'content',
        'explanation',
        'ai_generated',
        'source_document',
        'is_active',
    ];

    protected $casts = [
        'ai_generated' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'source_document');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}
