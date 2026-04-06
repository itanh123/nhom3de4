<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'model_name',
        'purpose',
        'api_key',
        'base_url',
        'temperature',
        'max_tokens',
        'default_prompt',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'temperature' => 'decimal:2',
        'max_tokens' => 'integer',
        'is_active' => 'boolean',
    ];

    public const PURPOSE_QUESTION_GENERATION = 'question_generation';
    public const PURPOSE_ANSWER_EXPLANATION = 'answer_explanation';
    public const PURPOSE_RESULT_EVALUATION = 'result_evaluation';
    public const PURPOSE_LEARNING_PATH = 'learning_path';
    public const PURPOSE_GENERAL = 'general';

    public static function purposes(): array
    {
        return [
            self::PURPOSE_QUESTION_GENERATION,
            self::PURPOSE_ANSWER_EXPLANATION,
            self::PURPOSE_RESULT_EVALUATION,
            self::PURPOSE_LEARNING_PATH,
            self::PURPOSE_GENERAL,
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPurpose($query, string $purpose)
    {
        return $query->where('purpose', $purpose);
    }
}
