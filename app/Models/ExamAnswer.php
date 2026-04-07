<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAnswer extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'result_id',
        'question_id',
        'answer_id',
        'text_answer',
        'is_correct',
        'ai_explanation',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function result(): BelongsTo
    {
        return $this->belongsTo(ExamResult::class, 'result_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class);
    }
}
