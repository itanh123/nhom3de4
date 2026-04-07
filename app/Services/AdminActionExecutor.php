<?php

namespace App\Services;

use App\Models\AiConfig;
use App\Models\Document;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\Question;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminActionExecutor
{
    protected ?int $userId = null;
    protected array $errors = [];

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function execute(string $action, array $payload): array
    {
        $this->errors = [];

        try {
            return match ($action) {
                AdminActionParser::ACTION_CREATE_TOPIC => $this->executeCreateTopic($payload),
                AdminActionParser::ACTION_UPDATE_TOPIC => $this->executeUpdateTopic($payload),
                AdminActionParser::ACTION_DELETE_TOPIC => $this->executeDeleteTopic($payload),
                AdminActionParser::ACTION_CREATE_USER => $this->executeCreateUser($payload),
                AdminActionParser::ACTION_UPDATE_USER => $this->executeUpdateUser($payload),
                AdminActionParser::ACTION_LOCK_USER => $this->executeLockUser($payload),
                AdminActionParser::ACTION_UNLOCK_USER => $this->executeUnlockUser($payload),
                AdminActionParser::ACTION_ASSIGN_ROLE => $this->executeAssignRole($payload),
                AdminActionParser::ACTION_CREATE_QUESTION => $this->executeCreateQuestion($payload),
                AdminActionParser::ACTION_UPDATE_QUESTION => $this->executeUpdateQuestion($payload),
                AdminActionParser::ACTION_DELETE_QUESTION => $this->executeDeleteQuestion($payload),
                AdminActionParser::ACTION_GENERATE_QUESTIONS_AI => $this->executeGenerateQuestionsAi($payload),
                AdminActionParser::ACTION_CREATE_EXAM => $this->executeCreateExam($payload),
                AdminActionParser::ACTION_UPDATE_EXAM => $this->executeUpdateExam($payload),
                AdminActionParser::ACTION_DELETE_EXAM => $this->executeDeleteExam($payload),
                AdminActionParser::ACTION_PUBLISH_EXAM => $this->executePublishExam($payload),
                AdminActionParser::ACTION_UNPUBLISH_EXAM => $this->executeUnpublishExam($payload),
                AdminActionParser::ACTION_CREATE_DOCUMENT => $this->executeCreateDocument($payload),
                AdminActionParser::ACTION_DELETE_DOCUMENT => $this->executeDeleteDocument($payload),
                AdminActionParser::ACTION_UPDATE_AI_CONFIG => $this->executeUpdateAiConfig($payload),
                AdminActionParser::ACTION_TOGGLE_AI_CONFIG => $this->executeToggleAiConfig($payload),
                AdminActionParser::ACTION_SUMMARIZE_REPORTS => $this->executeSummarizeReports($payload),
                default => ['success' => false, 'message' => "Hành động '{$action}' không được hỗ trợ."],
            };
        } catch (\Exception $e) {
            Log::error('AdminActionExecutor Error', [
                'action' => $action,
                'payload' => $payload,
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    protected function executeCreateTopic(array $payload): array
    {
        if (empty($payload['name'])) {
            return ['success' => false, 'message' => 'Thiếu tên chủ đề.'];
        }

        $parentId = null;
        if (!empty($payload['parent_topic'])) {
            $parent = Topic::where('name', 'like', '%' . $payload['parent_topic'] . '%')->first();
            if (!$parent) {
                return ['success' => false, 'message' => "Không tìm thấy chủ đề cha: {$payload['parent_topic']}"];
            }
            $parentId = $parent->id;
        }

        $topic = Topic::create([
            'name' => $payload['name'],
            'description' => $payload['description'] ?? null,
            'parent_id' => $parentId,
            'created_by' => $this->userId ?? Auth::id(),
        ]);

        ActivityLogger::createTopic($topic);

        return [
            'success' => true,
            'message' => "Đã tạo chủ đề '{$topic->name}' thành công.",
            'entity_type' => 'topics',
            'entity_id' => $topic->id,
        ];
    }

    protected function executeUpdateTopic(array $payload): array
    {
        $topic = $this->findTopic($payload);
        if (!$topic) {
            return ['success' => false, 'message' => 'Không tìm thấy chủ đề.'];
        }

        $updateData = [];
        if (!empty($payload['name'])) {
            $updateData['name'] = $payload['name'];
        }
        if (isset($payload['description'])) {
            $updateData['description'] = $payload['description'];
        }

        if (empty($updateData)) {
            return ['success' => false, 'message' => 'Không có dữ liệu để cập nhật.'];
        }

        $topic->update($updateData);
        ActivityLogger::updateTopic($topic);

        return [
            'success' => true,
            'message' => "Đã cập nhật chủ đề '{$topic->name}' thành công.",
            'entity_type' => 'topics',
            'entity_id' => $topic->id,
        ];
    }

    protected function executeDeleteTopic(array $payload): array
    {
        $topic = $this->findTopic($payload);
        if (!$topic) {
            return ['success' => false, 'message' => 'Không tìm thấy chủ đề.'];
        }

        if ($topic->hasChildren()) {
            return ['success' => false, 'message' => 'Không thể xóa chủ đề có chủ đề con.'];
        }

        ActivityLogger::deleteTopic($topic);
        $topic->delete();

        return [
            'success' => true,
            'message' => "Đã xóa chủ đề '{$topic->name}' thành công.",
        ];
    }

    protected function executeCreateUser(array $payload): array
    {
        if (empty($payload['email']) || empty($payload['name'])) {
            return ['success' => false, 'message' => 'Thiếu thông tin người dùng.'];
        }

        if (User::where('email', $payload['email'])->exists()) {
            return ['success' => false, 'message' => 'Email đã tồn tại.'];
        }

        $role = $payload['role'] ?? User::ROLE_STUDENT;
        if (!in_array($role, [User::ROLE_STUDENT, User::ROLE_TEACHER])) {
            $role = User::ROLE_STUDENT;
        }

        $user = User::create([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'password' => $payload['password'] ?? bcrypt('password123'),
            'role' => $role,
        ]);

        return [
            'success' => true,
            'message' => "Đã tạo người dùng '{$user->name}' với vai trò {$user->role}.",
            'entity_type' => 'users',
            'entity_id' => $user->id,
        ];
    }

    protected function executeUpdateUser(array $payload): array
    {
        $user = $this->findUser($payload);
        if (!$user) {
            return ['success' => false, 'message' => 'Không tìm thấy người dùng.'];
        }

        $updateData = [];
        if (!empty($payload['name'])) {
            $updateData['name'] = $payload['name'];
        }
        if (!empty($payload['email'])) {
            $updateData['email'] = $payload['email'];
        }

        if (empty($updateData)) {
            return ['success' => false, 'message' => 'Không có dữ liệu để cập nhật.'];
        }

        $user->update($updateData);

        return [
            'success' => true,
            'message' => "Đã cập nhật người dùng '{$user->name}' thành công.",
            'entity_type' => 'users',
            'entity_id' => $user->id,
        ];
    }

    protected function executeLockUser(array $payload): array
    {
        $user = $this->findUser($payload);
        if (!$user) {
            return ['success' => false, 'message' => 'Không tìm thấy người dùng.'];
        }

        if ($user->isAdmin()) {
            return ['success' => false, 'message' => 'Không thể khóa tài khoản admin.'];
        }

        $user->update(['is_active' => false]);

        return [
            'success' => true,
            'message' => "Đã khóa tài khoản '{$user->name}' thành công.",
            'entity_type' => 'users',
            'entity_id' => $user->id,
        ];
    }

    protected function executeUnlockUser(array $payload): array
    {
        $user = $this->findUser($payload);
        if (!$user) {
            return ['success' => false, 'message' => 'Không tìm thấy người dùng.'];
        }

        $user->update(['is_active' => true]);

        return [
            'success' => true,
            'message' => "Đã mở khóa tài khoản '{$user->name}' thành công.",
            'entity_type' => 'users',
            'entity_id' => $user->id,
        ];
    }

    protected function executeAssignRole(array $payload): array
    {
        if (empty($payload['user_email']) && empty($payload['user_identifier'])) {
            return ['success' => false, 'message' => 'Thiếu thông tin người dùng.'];
        }

        if (empty($payload['role'])) {
            return ['success' => false, 'message' => 'Thiếu vai trò.'];
        }

        $role = strtolower($payload['role']);
        if (!in_array($role, [User::ROLE_ADMIN, User::ROLE_TEACHER, User::ROLE_STUDENT])) {
            return ['success' => false, 'message' => 'Vai trò không hợp lệ.'];
        }

        $user = User::where('email', $payload['user_email'] ?? $payload['user_identifier'])->first();
        if (!$user) {
            $user = User::where('name', 'like', '%' . ($payload['user_email'] ?? $payload['user_identifier']) . '%')->first();
        }

        if (!$user) {
            return ['success' => false, 'message' => 'Không tìm thấy người dùng.'];
        }

        if ($user->isAdmin() && $role !== User::ROLE_ADMIN) {
            return ['success' => false, 'message' => 'Không thể thay đổi vai trò admin.'];
        }

        $oldRole = $user->role;
        $user->update(['role' => $role]);

        return [
            'success' => true,
            'message' => "Đã gán vai trò '{$role}' cho '{$user->name}' (trước đó: {$oldRole}).",
            'entity_type' => 'users',
            'entity_id' => $user->id,
        ];
    }

    protected function executeCreateQuestion(array $payload): array
    {
        if (empty($payload['topic_id']) && empty($payload['topic_name'])) {
            return ['success' => false, 'message' => 'Thiếu chủ đề cho câu hỏi.'];
        }

        $topicId = $payload['topic_id'];
        if (!$topicId && !empty($payload['topic_name'])) {
            $topic = Topic::where('name', 'like', '%' . $payload['topic_name'] . '%')->first();
            if (!$topic) {
                return ['success' => false, 'message' => "Không tìm thấy chủ đề: {$payload['topic_name']}"];
            }
            $topicId = $topic->id;
        }

        $question = Question::create([
            'topic_id' => $topicId,
            'created_by' => $this->userId ?? Auth::id(),
            'type' => $payload['type'] ?? Question::TYPE_SINGLE_CHOICE,
            'difficulty' => $payload['difficulty'] ?? Question::DIFFICULTY_MEDIUM,
            'content' => $payload['content'] ?? $payload['name'] ?? 'Câu hỏi mới',
            'is_active' => true,
        ]);

        ActivityLogger::createQuestion($question);

        return [
            'success' => true,
            'message' => 'Đã tạo câu hỏi mới thành công.',
            'entity_type' => 'questions',
            'entity_id' => $question->id,
        ];
    }

    protected function executeUpdateQuestion(array $payload): array
    {
        $question = $this->findQuestion($payload);
        if (!$question) {
            return ['success' => false, 'message' => 'Không tìm thấy câu hỏi.'];
        }

        $updateData = [];
        if (!empty($payload['content'])) {
            $updateData['content'] = $payload['content'];
        }
        if (!empty($payload['difficulty'])) {
            $updateData['difficulty'] = $payload['difficulty'];
        }
        if (!empty($payload['type'])) {
            $updateData['type'] = $payload['type'];
        }

        if (empty($updateData)) {
            return ['success' => false, 'message' => 'Không có dữ liệu để cập nhật.'];
        }

        $question->update($updateData);
        ActivityLogger::updateQuestion($question);

        return [
            'success' => true,
            'message' => 'Đã cập nhật câu hỏi thành công.',
            'entity_type' => 'questions',
            'entity_id' => $question->id,
        ];
    }

    protected function executeDeleteQuestion(array $payload): array
    {
        $question = $this->findQuestion($payload);
        if (!$question) {
            return ['success' => false, 'message' => 'Không tìm thấy câu hỏi.'];
        }

        ActivityLogger::deleteQuestion($question);
        $question->answers()->delete();
        $question->delete();

        return [
            'success' => true,
            'message' => 'Đã xóa câu hỏi thành công.',
        ];
    }

    protected function executeGenerateQuestionsAi(array $payload): array
    {
        $number = (int) ($payload['number'] ?? 5);
        $difficulty = $payload['difficulty'] ?? Question::DIFFICULTY_MEDIUM;
        $type = $payload['type'] ?? Question::TYPE_SINGLE_CHOICE;
        $topicName = $payload['topic_name'] ?? 'General';

        $topic = Topic::where('name', 'like', '%' . $topicName . '%')->first();
        if (!$topic) {
            $topic = Topic::create([
                'name' => $topicName,
                'created_by' => $this->userId ?? Auth::id(),
            ]);
        }

        $aiService = new AiService();
        $result = $aiService->generateQuestions([
            'topic' => $topic->name,
            'number' => $number,
            'difficulty' => $difficulty,
            'type' => $type,
            'prompt' => $payload['prompt'] ?? '',
        ]);

        if (isset($result['error'])) {
            return ['success' => false, 'message' => $result['error']];
        }

        $questionsData = $result['content'];

        if (empty($questionsData)) {
            return ['success' => false, 'message' => 'Không thể thu thập câu hỏi từ AI.'];
        }

        DB::beginTransaction();
        try {
            $savedCount = 0;
            foreach ($questionsData as $qData) {
                $question = Question::create([
                    'topic_id' => $topic->id,
                    'created_by' => $this->userId ?? Auth::id(),
                    'type' => $type,
                    'difficulty' => $difficulty,
                    'content' => $qData['content'],
                    'ai_generated' => true,
                    'is_active' => true,
                ]);

                foreach ($qData['answers'] as $order => $answer) {
                    $question->answers()->create([
                        'option_text' => $answer['option_text'],
                        'is_correct' => $answer['is_correct'] ?? false,
                        'display_order' => $order + 1,
                    ]);
                }
                $savedCount++;
            }

            DB::commit();
            ActivityLogger::aiGenerateQuestions($savedCount, $type);

            return [
                'success' => true,
                'message' => "Đã tạo {$savedCount} câu hỏi về '{$topic->name}' bằng AI.",
                'entity_type' => 'questions',
                'entity_id' => $topic->id,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => 'Lỗi khi lưu: ' . $e->getMessage()];
        }
    }

    protected function parseAiQuestions(string $content): array
    {
        $content = trim($content);

        if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
            $content = $matches[1];
        } elseif (preg_match('/```\s*(.*?)\s*```/s', $content, $matches)) {
            $content = $matches[1];
        }

        $data = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            return $this->normalizeParsedQuestions($data);
        }

        return [];
    }

    protected function normalizeParsedQuestions(array $data): array
    {
        $questions = [];

        foreach ($data as $item) {
            if (!isset($item['content']) || !isset($item['answers'])) {
                continue;
            }

            $answers = [];
            foreach ($item['answers'] as $ans) {
                $answers[] = [
                    'option_text' => $ans['option_text'] ?? $ans['text'] ?? '',
                    'is_correct' => $ans['is_correct'] ?? $ans['correct'] ?? false,
                ];
            }

            if (count($answers) < 2) {
                continue;
            }

            $questions[] = [
                'content' => trim($item['content']),
                'answers' => $answers,
            ];
        }

        return $questions;
    }

    protected function executeCreateExam(array $payload): array
    {
        if (empty($payload['exam_title'])) {
            return ['success' => false, 'message' => 'Thiếu tiêu đề bài thi.'];
        }

        $topicId = null;
        if (!empty($payload['topic_name'])) {
            $topic = Topic::where('name', 'like', '%' . $payload['topic_name'] . '%')->first();
            if ($topic) {
                $topicId = $topic->id;
            }
        }

        $exam = Exam::create([
            'topic_id' => $topicId,
            'created_by' => $this->userId ?? Auth::id(),
            'title' => $payload['exam_title'],
            'description' => $payload['description'] ?? null,
            'duration_mins' => $payload['duration'] ?? 30,
            'pass_score' => $payload['pass_score'] ?? 50,
            'status' => Exam::STATUS_DRAFT,
            'is_published' => false,
            'is_active' => true,
        ]);

        ActivityLogger::createExam($exam);

        return [
            'success' => true,
            'message' => "Đã tạo bài thi '{$exam->title}' thành công.",
            'entity_type' => 'exams',
            'entity_id' => $exam->id,
        ];
    }

    protected function executeUpdateExam(array $payload): array
    {
        $exam = $this->findExam($payload);
        if (!$exam) {
            return ['success' => false, 'message' => 'Không tìm thấy bài thi.'];
        }

        $updateData = [];
        if (!empty($payload['title'])) {
            $updateData['title'] = $payload['title'];
        }
        if (isset($payload['duration'])) {
            $updateData['duration_mins'] = (int) $payload['duration'];
        }
        if (isset($payload['pass_score'])) {
            $updateData['pass_score'] = (int) $payload['pass_score'];
        }

        if (empty($updateData)) {
            return ['success' => false, 'message' => 'Không có dữ liệu để cập nhật.'];
        }

        $exam->update($updateData);
        ActivityLogger::updateExam($exam);

        return [
            'success' => true,
            'message' => "Đã cập nhật bài thi '{$exam->title}' thành công.",
            'entity_type' => 'exams',
            'entity_id' => $exam->id,
        ];
    }

    protected function executeDeleteExam(array $payload): array
    {
        $exam = $this->findExam($payload);
        if (!$exam) {
            return ['success' => false, 'message' => 'Không tìm thấy bài thi.'];
        }

        ActivityLogger::deleteExam($exam);
        $exam->examQuestions()->delete();
        $exam->delete();

        return [
            'success' => true,
            'message' => "Đã xóa bài thi '{$exam->title}' thành công.",
        ];
    }

    protected function executePublishExam(array $payload): array
    {
        $exam = $this->findExam($payload);
        if (!$exam) {
            return ['success' => false, 'message' => 'Không tìm thấy bài thi.'];
        }

        $exam->update(['is_published' => true]);

        return [
            'success' => true,
            'message' => "Đã công bố bài thi '{$exam->title}'.",
            'entity_type' => 'exams',
            'entity_id' => $exam->id,
        ];
    }

    protected function executeUnpublishExam(array $payload): array
    {
        $exam = $this->findExam($payload);
        if (!$exam) {
            return ['success' => false, 'message' => 'Không tìm thấy bài thi.'];
        }

        $exam->update(['is_published' => false]);

        return [
            'success' => true,
            'message' => "Đã ẩn bài thi '{$exam->title}'.",
            'entity_type' => 'exams',
            'entity_id' => $exam->id,
        ];
    }

    protected function executeCreateDocument(array $payload): array
    {
        return ['success' => false, 'message' => 'Cần upload file để tạo tài liệu. Vui lòng sử dụng trang quản lý tài liệu.'];
    }

    protected function executeDeleteDocument(array $payload): array
    {
        $document = $this->findDocument($payload);
        if (!$document) {
            return ['success' => false, 'message' => 'Không tìm thấy tài liệu.'];
        }

        ActivityLogger::deleteDocument($document);
        $document->delete();

        return [
            'success' => true,
            'message' => "Đã xóa tài liệu '{$document->file_name}'.",
        ];
    }

    protected function executeUpdateAiConfig(array $payload): array
    {
        return ['success' => false, 'message' => 'Cần sử dụng trang quản lý AI Config để cập nhật.'];
    }

    protected function executeToggleAiConfig(array $payload): array
    {
        $config = AiConfig::where('model_name', 'like', '%' . ($payload['entity_name'] ?? '') . '%')->first();
        if (!$config) {
            $config = AiConfig::find($payload['entity_id'] ?? null);
        }

        if (!$config) {
            return ['success' => false, 'message' => 'Không tìm thấy cấu hình AI.'];
        }

        $newStatus = !$config->is_active;
        if ($newStatus) {
            AiConfig::where('purpose', $config->purpose)->where('id', '!=', $config->id)->update(['is_active' => false]);
        }
        $config->update(['is_active' => $newStatus]);
        ActivityLogger::toggleAiConfig($config);

        return [
            'success' => true,
            'message' => ($newStatus ? 'Đã bật' : 'Đã tắt') . " cấu hình AI '{$config->model_name}'.",
            'entity_type' => 'ai_configs',
            'entity_id' => $config->id,
        ];
    }

    protected function executeSummarizeReports(array $payload): array
    {
        $totalUsers = User::count();
        $totalTopics = Topic::count();
        $totalQuestions = Question::count();
        $totalExams = Exam::count();
        $totalDocuments = Document::count();

        $recentExams = Exam::with('topic')->orderByDesc('created_at')->limit(5)->get();
        $recentQuestions = Question::with('topic')->orderByDesc('created_at')->limit(5)->get();

        $summary = "📊 **Tổng quan hệ thống**\n\n";
        $summary .= "- 👥 Người dùng: {$totalUsers}\n";
        $summary .= "- 📁 Chủ đề: {$totalTopics}\n";
        $summary .= "- ❓ Câu hỏi: {$totalQuestions}\n";
        $summary .= "- 📝 Bài thi: {$totalExams}\n";
        $summary .= "- 📄 Tài liệu: {$totalDocuments}\n\n";
        $summary .= "**Bài thi gần đây:**\n";
        foreach ($recentExams as $exam) {
            $topicName = $exam->topic?->name ?? 'Không có';
            $summary .= "- {$exam->title} ({$topicName})\n";
        }

        return [
            'success' => true,
            'message' => $summary,
        ];
    }

    protected function findTopic(array $payload): ?Topic
    {
        if (!empty($payload['topic_id'])) {
            return Topic::find($payload['topic_id']);
        }
        if (!empty($payload['entity_name'])) {
            return Topic::where('name', 'like', '%' . $payload['entity_name'] . '%')->first();
        }
        if (!empty($payload['name'])) {
            return Topic::where('name', 'like', '%' . $payload['name'] . '%')->first();
        }
        return null;
    }

    protected function findUser(array $payload): ?User
    {
        if (!empty($payload['user_id'])) {
            return User::find($payload['user_id']);
        }
        if (!empty($payload['user_email'])) {
            return User::where('email', $payload['user_email'])->first();
        }
        if (!empty($payload['entity_name'])) {
            return User::where('name', 'like', '%' . $payload['entity_name'] . '%')->first();
        }
        return null;
    }

    protected function findQuestion(array $payload): ?Question
    {
        if (!empty($payload['question_id'])) {
            return Question::find($payload['question_id']);
        }
        if (!empty($payload['entity_id'])) {
            return Question::find($payload['entity_id']);
        }
        if (!empty($payload['entity_name'])) {
            return Question::where('content', 'like', '%' . $payload['entity_name'] . '%')->first();
        }
        return null;
    }

    protected function findExam(array $payload): ?Exam
    {
        if (!empty($payload['exam_id'])) {
            return Exam::find($payload['exam_id']);
        }
        if (!empty($payload['entity_id'])) {
            return Exam::find($payload['entity_id']);
        }
        if (!empty($payload['entity_name'])) {
            return Exam::where('title', 'like', '%' . $payload['entity_name'] . '%')->first();
        }
        if (!empty($payload['exam_title'])) {
            return Exam::where('title', 'like', '%' . $payload['exam_title'] . '%')->first();
        }
        return null;
    }

    protected function findDocument(array $payload): ?Document
    {
        if (!empty($payload['document_id'])) {
            return Document::find($payload['document_id']);
        }
        if (!empty($payload['entity_id'])) {
            return Document::find($payload['entity_id']);
        }
        if (!empty($payload['entity_name'])) {
            return Document::where('file_name', 'like', '%' . $payload['entity_name'] . '%')->first();
        }
        return null;
    }
}
