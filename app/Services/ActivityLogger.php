<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function log(string $action, ?string $entityType = null, ?int $entityId = null, ?string $description = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }

    public static function login(): ActivityLog
    {
        return self::log('login', 'user', Auth::id(), 'Đăng nhập hệ thống');
    }

    public static function logout(): ActivityLog
    {
        return self::log('logout', 'user', Auth::id(), 'Đăng xuất hệ thống');
    }

    public static function register(): ActivityLog
    {
        return self::log('register', 'user', Auth::id(), 'Đăng ký tài khoản mới');
    }

    public static function createTopic($topic): ActivityLog
    {
        return self::log('create_topic', 'topics', $topic->id, "Tạo chủ đề: {$topic->name}");
    }

    public static function updateTopic($topic): ActivityLog
    {
        return self::log('update_topic', 'topics', $topic->id, "Cập nhật chủ đề: {$topic->name}");
    }

    public static function deleteTopic($topic): ActivityLog
    {
        return self::log('delete_topic', 'topics', $topic->id, "Xóa chủ đề: {$topic->name}");
    }

    public static function createQuestion($question): ActivityLog
    {
        return self::log('create_question', 'questions', $question->id, "Tạo câu hỏi mới");
    }

    public static function updateQuestion($question): ActivityLog
    {
        return self::log('update_question', 'questions', $question->id, "Cập nhật câu hỏi");
    }

    public static function deleteQuestion($question): ActivityLog
    {
        return self::log('delete_question', 'questions', $question->id, "Xóa câu hỏi");
    }

    public static function createExam($exam): ActivityLog
    {
        return self::log('create_exam', 'exams', $exam->id, "Tạo bài thi: {$exam->title}");
    }

    public static function updateExam($exam): ActivityLog
    {
        return self::log('update_exam', 'exams', $exam->id, "Cập nhật bài thi: {$exam->title}");
    }

    public static function deleteExam($exam): ActivityLog
    {
        return self::log('delete_exam', 'exams', $exam->id, "Xóa bài thi: {$exam->title}");
    }

    public static function takeExam($result): ActivityLog
    {
        return self::log('take_exam', 'exam_results', $result->id, "Làm bài thi #{$result->exam_id}");
    }

    public static function importQuestions($import): ActivityLog
    {
        return self::log('import_questions', 'import_histories', $import->id, "Nhập {$import->success_rows}/{$import->total_rows} câu hỏi từ file");
    }

    public static function uploadDocument($document): ActivityLog
    {
        return self::log('upload_document', 'documents', $document->id, "Tải lên tài liệu: {$document->file_name}");
    }

    public static function deleteDocument($document): ActivityLog
    {
        return self::log('delete_document', 'documents', $document->id, "Xóa tài liệu: {$document->file_name}");
    }

    public static function createAiConfig($config): ActivityLog
    {
        return self::log('create_ai_config', 'ai_configs', $config->id, "Tạo cấu hình AI: {$config->provider} - {$config->model_name}");
    }

    public static function updateAiConfig($config): ActivityLog
    {
        return self::log('update_ai_config', 'ai_configs', $config->id, "Cập nhật cấu hình AI: {$config->provider} - {$config->model_name}");
    }

    public static function deleteAiConfig($config): ActivityLog
    {
        return self::log('delete_ai_config', 'ai_configs', $config->id, "Xóa cấu hình AI: {$config->provider} - {$config->model_name}");
    }

    public static function toggleAiConfig($config): ActivityLog
    {
        $status = $config->is_active ? 'bật' : 'tắt';
        return self::log('toggle_ai_config', 'ai_configs', $config->id, "{$status} cấu hình AI: {$config->provider} - {$config->model_name}");
    }

    public static function aiGenerateQuestions(?int $questionCount = null, ?string $purpose = null): ActivityLog
    {
        $desc = 'Tạo câu hỏi bằng AI';
        if ($questionCount) {
            $desc .= " ({$questionCount} câu)";
        }
        if ($purpose) {
            $desc .= " - {$purpose}";
        }
        return self::log('ai_generate_questions', 'questions', null, $desc);
    }

    public static function aiExplainAnswer(?int $questionId = null): ActivityLog
    {
        return self::log('ai_explain_answer', 'questions', $questionId, 'Tạo giải thích đáp án bằng AI');
    }

    public static function aiEvaluateResult(?int $resultId = null): ActivityLog
    {
        return self::log('ai_evaluate_result', 'exam_results', $resultId, 'Đánh giá kết quả bằng AI');
    }

    public static function aiGenerateLearningPath(?int $resultId = null): ActivityLog
    {
        return self::log('ai_generate_learning_path', 'exam_results', $resultId, 'Tạo lộ trình học tập bằng AI');
    }

    public static function aiAgentCommand(string $action, ?string $result = null): ActivityLog
    {
        $desc = 'AI Agent: ' . $action;
        if ($result) {
            $desc .= ' - ' . $result;
        }
        return self::log('ai_agent_command', 'ai_chat_commands', null, $desc);
    }

    public static function createChatSession($session): ActivityLog
    {
        return self::log('create_chat_session', 'chat_sessions', $session->id, "Tạo cuộc trò chuyện: {$session->title}");
    }

    public static function sendChatMessage($session): ActivityLog
    {
        return self::log('send_chat_message', 'chat_sessions', $session->id, "Gửi tin nhắn trong: {$session->title}");
    }
}
