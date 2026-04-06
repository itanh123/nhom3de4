<?php

namespace App\Models;

use App\Models\ExamResult;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'shuffle_q' => 'boolean',
        'shuffle_a' => 'boolean',
        'show_explain' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function results()
    {
        return $this->hasMany(ExamResult::class);
    }
}
