<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

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

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
