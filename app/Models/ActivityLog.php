<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'description',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'entity_id' => 'integer',
        'created_at' => 'datetime',
    ];

    public const ACTION_LOGIN = 'login';
    public const ACTION_LOGOUT = 'logout';
    public const ACTION_CREATE_TOPIC = 'create_topic';
    public const ACTION_UPDATE_TOPIC = 'update_topic';
    public const ACTION_DELETE_TOPIC = 'delete_topic';
    public const ACTION_CREATE_EXAM = 'create_exam';
    public const ACTION_UPDATE_EXAM = 'update_exam';
    public const ACTION_DELETE_EXAM = 'delete_exam';
    public const ACTION_CREATE_QUESTION = 'create_question';
    public const ACTION_IMPORT_QUESTIONS = 'import_questions';
    public const ACTION_TAKE_EXAM = 'take_exam';

    public static function actions(): array
    {
        return [
            self::ACTION_LOGIN,
            self::ACTION_LOGOUT,
            self::ACTION_CREATE_TOPIC,
            self::ACTION_UPDATE_TOPIC,
            self::ACTION_DELETE_TOPIC,
            self::ACTION_CREATE_EXAM,
            self::ACTION_UPDATE_EXAM,
            self::ACTION_DELETE_EXAM,
            self::ACTION_CREATE_QUESTION,
            self::ACTION_IMPORT_QUESTIONS,
            self::ACTION_TAKE_EXAM,
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByEntity($query, string $type, int $id)
    {
        return $query->where('entity_type', $type)->where('entity_id', $id);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
