<?php

namespace App\Services;

use App\Models\AiConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminActionParser
{
    public const ACTION_CREATE_TOPIC = 'create_topic';
    public const ACTION_UPDATE_TOPIC = 'update_topic';
    public const ACTION_DELETE_TOPIC = 'delete_topic';
    public const ACTION_CREATE_USER = 'create_user';
    public const ACTION_UPDATE_USER = 'update_user';
    public const ACTION_LOCK_USER = 'lock_user';
    public const ACTION_UNLOCK_USER = 'unlock_user';
    public const ACTION_ASSIGN_ROLE = 'assign_role';
    public const ACTION_CREATE_QUESTION = 'create_question';
    public const ACTION_UPDATE_QUESTION = 'update_question';
    public const ACTION_DELETE_QUESTION = 'delete_question';
    public const ACTION_GENERATE_QUESTIONS_AI = 'generate_questions_ai';
    public const ACTION_CREATE_EXAM = 'create_exam';
    public const ACTION_UPDATE_EXAM = 'update_exam';
    public const ACTION_DELETE_EXAM = 'delete_exam';
    public const ACTION_PUBLISH_EXAM = 'publish_exam';
    public const ACTION_UNPUBLISH_EXAM = 'unpublish_exam';
    public const ACTION_CREATE_DOCUMENT = 'create_document';
    public const ACTION_DELETE_DOCUMENT = 'delete_document';
    public const ACTION_CREATE_AI_CONFIG = 'create_ai_config';
    public const ACTION_UPDATE_AI_CONFIG = 'update_ai_config';
    public const ACTION_TOGGLE_AI_CONFIG = 'toggle_ai_config';
    public const ACTION_IMPORT_QUESTIONS = 'import_questions';
    public const ACTION_SUMMARIZE_REPORTS = 'summarize_reports';

    public const WHITELISTED_ACTIONS = [
        self::ACTION_CREATE_TOPIC,
        self::ACTION_UPDATE_TOPIC,
        self::ACTION_DELETE_TOPIC,
        self::ACTION_CREATE_USER,
        self::ACTION_UPDATE_USER,
        self::ACTION_LOCK_USER,
        self::ACTION_UNLOCK_USER,
        self::ACTION_ASSIGN_ROLE,
        self::ACTION_CREATE_QUESTION,
        self::ACTION_UPDATE_QUESTION,
        self::ACTION_DELETE_QUESTION,
        self::ACTION_GENERATE_QUESTIONS_AI,
        self::ACTION_CREATE_EXAM,
        self::ACTION_UPDATE_EXAM,
        self::ACTION_DELETE_EXAM,
        self::ACTION_PUBLISH_EXAM,
        self::ACTION_UNPUBLISH_EXAM,
        self::ACTION_CREATE_DOCUMENT,
        self::ACTION_DELETE_DOCUMENT,
        self::ACTION_CREATE_AI_CONFIG,
        self::ACTION_UPDATE_AI_CONFIG,
        self::ACTION_TOGGLE_AI_CONFIG,
        self::ACTION_IMPORT_QUESTIONS,
        self::ACTION_SUMMARIZE_REPORTS,
    ];

    public const AUTO_EXECUTE_ACTIONS = [
        self::ACTION_CREATE_TOPIC,
        self::ACTION_UPDATE_TOPIC,
        self::ACTION_CREATE_QUESTION,
        self::ACTION_UPDATE_QUESTION,
        self::ACTION_CREATE_EXAM,
        self::ACTION_UPDATE_EXAM,
        self::ACTION_PUBLISH_EXAM,
        self::ACTION_UNPUBLISH_EXAM,
        self::ACTION_CREATE_DOCUMENT,
        self::ACTION_SUMMARIZE_REPORTS,
    ];

    public const CONFIRM_REQUIRED_ACTIONS = [
        self::ACTION_DELETE_TOPIC,
        self::ACTION_DELETE_QUESTION,
        self::ACTION_DELETE_EXAM,
        self::ACTION_DELETE_DOCUMENT,
        self::ACTION_LOCK_USER,
        self::ACTION_UNLOCK_USER,
        self::ACTION_UPDATE_AI_CONFIG,
        self::ACTION_TOGGLE_AI_CONFIG,
        self::ACTION_CREATE_AI_CONFIG,
        self::ACTION_IMPORT_QUESTIONS,
    ];

    protected array $dangerousPatterns = [
        '/\b(drop|delete\s+from|truncate|alter)\s+(table|database)/i',
        '/\b(exec|execute|shell|system|passthru|popen)\s*\(/i',
        '/\b(eval|base64_decode|assert)\s*\(/i',
        '/\{\{\s*.*\}\}/i',
        '/\{\!!.*\!\!/i',
    ];

    public function parse(string $message): array
    {
        $message = trim($message);

        if ($this->containsDangerousPatterns($message)) {
            return [
                'action' => null,
                'payload' => null,
                'error' => 'Lệnh chứa yêu cầu bị cấm vì lý do bảo mật.',
                'blocked' => true,
            ];
        }

        $result = $this->parseWithAi($message);
        
        if (isset($result['error']) || isset($result['blocked'])) {
            return $result;
        }

        if (!$result['action']) {
            $result = $this->parseDeterministic($message);
        }

        if (!$result['action']) {
            return [
                'action' => null,
                'payload' => null,
                'error' => 'Không thể hiểu lệnh. Vui lòng sử dụng các lệnh như: "Tạo chủ đề [tên]", "Xóa câu hỏi [id]", "Công bố bài thi [tên]".',
                'blocked' => false,
            ];
        }

        return $result;
    }

    protected function parseWithAi(string $message): array
    {
        $config = AiConfig::active()->byPurpose(AiConfig::PURPOSE_GENERAL)->first();

        if (!$config) {
            return ['action' => null, 'payload' => null];
        }

        try {
            $systemPrompt = $this->getParserSystemPrompt();
            $result = $this->callAiForParsing($config, $systemPrompt, $message);

            if (isset($result['error'])) {
                return ['action' => null, 'payload' => null];
            }

            $parsed = json_decode($result['content'], true);
            
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($parsed)) {
                Log::warning('Admin AI Parser: Invalid JSON from AI', ['content' => $result['content']]);
                return ['action' => null, 'payload' => null];
            }

            if (!isset($parsed['action']) || !in_array($parsed['action'], self::WHITELISTED_ACTIONS)) {
                return [
                    'action' => null,
                    'payload' => null,
                    'error' => "Hành động '{$parsed['action']}' không được phép. Chỉ hỗ trợ các hành động trong whitelist.",
                    'blocked' => true,
                ];
            }

            return $parsed;

        } catch (\Exception $e) {
            Log::error('Admin AI Parser Error', ['message' => $e->getMessage()]);
            return ['action' => null, 'payload' => null];
        }
    }

    protected function callAiForParsing(AiConfig $config, string $systemPrompt, string $prompt): array
    {
        $aiService = new AiService();
        $apiKey = '';
        if (!empty($config->api_key)) {
            try {
                $apiKey = decrypt($config->api_key);
            } catch (\Exception $e) {
                $apiKey = $config->api_key;
            }
        }

        if (empty($apiKey)) {
            return ['error' => 'API Key trống.'];
        }

        $temperature = 0.1; // Low temperature for parsing
        $maxTokens = 1000;

        if ($config->provider === 'openrouter') {
            return $aiService->callOpenRouter($apiKey, $config->model_name, $systemPrompt, $prompt, $temperature, $maxTokens);
        }

        if ($config->provider === 'groq') {
            return $aiService->callGroq($apiKey, $config->model_name, $systemPrompt, $prompt, $temperature, $maxTokens);
        }

        return ['error' => "Provider '{$config->provider}' không được hỗ trợ cho AI Agent."];
    }

    protected function getParserSystemPrompt(): string
    {
        return "Bạn là một AI Agent Parser cho hệ thống Quiz. Nhiệm vụ của bạn là phân tích câu lệnh của quản trị viên và chuyển đổi thành hành động cụ thể.

CHỈ ĐỊNH DANH SÁCH HÀNH ĐỘNG ĐƯỢC PHÉP (whitelist):
- create_topic: Tạo chủ đề mới
- update_topic: Cập nhật chủ đề
- delete_topic: Xóa chủ đề
- create_user: Tạo người dùng mới
- update_user: Cập nhật người dùng
- lock_user: Khóa tài khoản người dùng
- unlock_user: Mở khóa tài khoản người dùng
- assign_role: Gán vai trò cho người dùng
- create_question: Tạo câu hỏi mới
- update_question: Cập nhật câu hỏi
- delete_question: Xóa câu hỏi
- generate_questions_ai: Tạo câu hỏi bằng AI
- create_exam: Tạo bài thi mới
- update_exam: Cập nhật bài thi
- delete_exam: Xóa bài thi
- publish_exam: Công bố/hiển thị bài thi
- unpublish_exam: Ẩn bài thi
- create_document: Tạo tài liệu mới
- delete_document: Xóa tài liệu
- create_ai_config: Tạo cấu hình AI
- update_ai_config: Cập nhật cấu hình AI
- toggle_ai_config: Bật/tắt cấu hình AI
- import_questions: Nhập câu hỏi từ file
- summarize_reports: Tổng hợp báo cáo

QUY TẮC:
1. LUÔN trả về JSON với các trường: action, payload (object chứa các tham số cần thiết), confidence (0-1)
2. Nếu lệnh không hợp lệ hoặc nằm ngoài whitelist, trả về action: null
3. payload có thể bao gồm: name, topic_id, topic_name, parent_topic, question_count, difficulty, type, exam_title, duration, pass_score, user_email, role, entity_id, entity_name, confirm_warning

VÍ DỤ:
Input: \"Tạo chủ đề PHP Basics dưới Công nghệ thông tin\"
Output: {\"action\": \"create_topic\", \"payload\": {\"name\": \"PHP Basics\", \"parent_topic\": \"Công nghệ thông tin\"}, \"confidence\": 0.95}

Input: \"Tạo 10 câu hỏi dễ về Laravel\"
Output: {\"action\": \"generate_questions_ai\", \"payload\": {\"number\": 10, \"difficulty\": \"easy\", \"topic_name\": \"Laravel\"}, \"confidence\": 0.9}

Input: \"Xóa bài thi TOEIC\"
Output: {\"action\": \"delete_exam\", \"payload\": {\"entity_name\": \"TOEIC\"}, \"confidence\": 0.85}

Input: \"Công bố bài thi Laravel Beginner Quiz\"
Output: {\"action\": \"publish_exam\", \"payload\": {\"entity_name\": \"Laravel Beginner Quiz\"}, \"confidence\": 0.95}

Input: \"Khóa tài khoản student1\"
Output: {\"action\": \"lock_user\", \"payload\": {\"entity_name\": \"student1\"}, \"confidence\": 0.9}

Input: \"Thay đổi vai trò abc@gmail.com thành teacher\"
Output: {\"action\": \"assign_role\", \"payload\": {\"user_email\": \"abc@gmail.com\", \"role\": \"teacher\"}, \"confidence\": 0.95}

TRẢ LỜI CHỈ ĐỊNH DẠNG JSON, không có text khác.";
    }

    protected function callOpenRouter(string $apiKey, string $model, string $systemPrompt, string $prompt, float $temperature, int $maxTokens): array
    {
        try {
            $response = Http::withoutVerifying()
                ->timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'HTTP-Referer' => config('app.url', 'http://localhost'),
                    'X-Title' => 'Admin AI Agent Parser',
                ])
                ->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => $temperature,
                    'max_tokens' => $maxTokens,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content', '');
                return ['content' => trim($content)];
            }

            return ['error' => 'OpenRouter error: ' . $response->body()];

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    protected function callGroq(string $apiKey, string $model, string $systemPrompt, string $prompt, float $temperature, int $maxTokens): array
    {
        try {
            $response = Http::withoutVerifying()
                ->timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => $temperature,
                    'max_tokens' => $maxTokens,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content', '');
                return ['content' => trim($content)];
            }

            return ['error' => 'Groq error: ' . $response->body()];

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    protected function callOpenAI(string $apiKey, string $model, string $systemPrompt, string $prompt, float $temperature, int $maxTokens, ?string $baseUrl): array
    {
        try {
            $url = $baseUrl ? rtrim($baseUrl, '/') . '/chat/completions' : 'https://api.openai.com/v1/chat/completions';

            $response = Http::withoutVerifying()
                ->withToken($apiKey)
                ->timeout(60)
                ->post($url, [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => $temperature,
                    'max_tokens' => $maxTokens,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content', '');
                return ['content' => trim($content)];
            }

            return ['error' => 'OpenAI error: ' . $response->body()];

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    protected function callAnthropic(string $apiKey, string $model, string $systemPrompt, string $prompt, int $maxTokens, ?string $baseUrl): array
    {
        try {
            $url = $baseUrl ? rtrim($baseUrl, '/') . '/v1/messages' : 'https://api.anthropic.com/v1/messages';

            $response = Http::withoutVerifying()->withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(60)->post($url, [
                'model' => $model,
                'max_tokens' => $maxTokens,
                'system' => $systemPrompt,
                'messages' => [['role' => 'user', 'content' => $prompt]],
            ]);

            if ($response->successful()) {
                $content = $response->json('content.0.text', '');
                return ['content' => trim($content)];
            }

            return ['error' => 'Anthropic error: ' . $response->body()];

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    protected function callGoogle(string $apiKey, string $model, string $systemPrompt, string $prompt, float $temperature, int $maxTokens): array
    {
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            $response = Http::withoutVerifying()->timeout(60)->post($url, [
                'contents' => [['parts' => [['text' => $systemPrompt . "\n\n" . $prompt]]]],
                'generationConfig' => [
                    'temperature' => $temperature,
                    'maxOutputTokens' => $maxTokens,
                ],
            ]);

            if ($response->successful()) {
                $content = $response->json('candidates.0.content.parts.0.text', '');
                return ['content' => trim($content)];
            }

            return ['error' => 'Google AI error: ' . $response->body()];

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function parseDeterministic(string $message): array
    {
        $message = trim($message);
        $lower = mb_strtolower($message);

        $patterns = [
            ['pattern' => '/^tạo\s+chủ\s*đề\s+(.+?)\s*(?:dưới|dưới\s+chủ\s*đề|thuộc)\s+(.+)/i', 'action' => self::ACTION_CREATE_TOPIC, 'fields' => ['name', 'parent_topic']],
            ['pattern' => '/^(?:tạo|tạo\s+mới)\s+chủ\s*đề\s+(.+)/i', 'action' => self::ACTION_CREATE_TOPIC, 'fields' => ['name']],
            ['pattern' => '/^(?:tạo|tạo\s+mới)\s+(?:(\d+)\s+)?câu\s+hỏi\s*(?:dễ|dễ\s+|trung\s+bình|khó)?\s*(?:về|là)\s*(.+)/i', 'action' => self::ACTION_GENERATE_QUESTIONS_AI, 'fields' => ['number', 'topic_name']],
            ['pattern' => '/^(?:tạo|tạo\s+mới)\s+bài\s+thi\s+(?:cho\s+)?(.+?)\s*(?:với|dành\s+cho)?\s*(?:(\d+)\s+)?câu?\s+hỏi?\s*(?:và\s+(\d+)\s+p(?:hú|t))/i', 'action' => self::ACTION_CREATE_EXAM, 'fields' => ['exam_title', 'question_count', 'duration']],
            ['pattern' => '/^(?:tạo|tạo\s+mới)\s+bài\s+thi\s+(.+)/i', 'action' => self::ACTION_CREATE_EXAM, 'fields' => ['exam_title']],
            ['pattern' => '/^(?:xóa|delete|remove)\s+(?:bài\s+thi|đề\s+thi)\s+(.+)/i', 'action' => self::ACTION_DELETE_EXAM, 'fields' => ['entity_name']],
            ['pattern' => '/^(?:xóa|delete|remove)\s+(?:chủ\s*đề|topic)\s+(.+)/i', 'action' => self::ACTION_DELETE_TOPIC, 'fields' => ['entity_name']],
            ['pattern' => '/^(?:xóa|delete|remove)\s+(?:câu\s+hỏi|question)\s+(.+)/i', 'action' => self::ACTION_DELETE_QUESTION, 'fields' => ['entity_name']],
            ['pattern' => '/^(?:công\s+bố|publish|hiển\s+thị|đăng)\s+(?:bài\s+thi|đề\s+thi)\s+(.+)/i', 'action' => self::ACTION_PUBLISH_EXAM, 'fields' => ['entity_name']],
            ['pattern' => '/^(?:ẩn|hide|unpublish)\s+(?:bài\s+thi|đề\s+thi)\s+(.+)/i', 'action' => self::ACTION_UNPUBLISH_EXAM, 'fields' => ['entity_name']],
            ['pattern' => '/^(?:khóa|lock|block)\s+(?:tài\s+khoản\s+)?(.+)/i', 'action' => self::ACTION_LOCK_USER, 'fields' => ['entity_name']],
            ['pattern' => '/^(?:mở\s+khóa|unlock|unblock)\s+(?:tài\s+khoản\s+)?(.+)/i', 'action' => self::ACTION_UNLOCK_USER, 'fields' => ['entity_name']],
            ['pattern' => '/^(?:thay\s+đổi|gán)\s+(?:vai\s+trò|role)\s+(?:của\s+)?(.+?)\s+(?:thành|to|=)\s+(admin|teacher|student)/i', 'action' => self::ACTION_ASSIGN_ROLE, 'fields' => ['user_identifier', 'role']],
            ['pattern' => '/^(?:thay\s+đổi|gán)\s+(?:user|tài\s+khoản)\s+(.+?)\s+(?:thành|to|=)\s+(admin|teacher|student)/i', 'action' => self::ACTION_ASSIGN_ROLE, 'fields' => ['user_identifier', 'role']],
            ['pattern' => '/^cập\s+nhật\s+chủ\s*đề\s+(.+)/i', 'action' => self::ACTION_UPDATE_TOPIC, 'fields' => ['entity_name']],
            ['pattern' => '/^tạo\s+người\s+dùng\s+(.+)/i', 'action' => self::ACTION_CREATE_USER, 'fields' => ['user_identifier']],
            ['pattern' => '/^xóa\s+tài\s+liệu\s+(.+)/i', 'action' => self::ACTION_DELETE_DOCUMENT, 'fields' => ['entity_name']],
            ['pattern' => '/^bật\s+cấu\s+hình\s+ai\s+(.+)/i', 'action' => self::ACTION_TOGGLE_AI_CONFIG, 'fields' => ['entity_name']],
            ['pattern' => '/^tắt\s+cấu\s+hình\s+ai\s+(.+)/i', 'action' => self::ACTION_TOGGLE_AI_CONFIG, 'fields' => ['entity_name']],
            ['pattern' => '/^nhập\s+câu\s+hỏi/i', 'action' => self::ACTION_IMPORT_QUESTIONS, 'fields' => []],
            ['pattern' => '/^tổng\s+hợp\s+báo\s+cáo/i', 'action' => self::ACTION_SUMMARIZE_REPORTS, 'fields' => []],
        ];

        foreach ($patterns as $p) {
            if (preg_match($p['pattern'], $message, $matches)) {
                $payload = [];
                foreach ($p['fields'] as $index => $field) {
                    if (isset($matches[$index + 1])) {
                        $payload[$field] = trim($matches[$index + 1]);
                    }
                }
                return [
                    'action' => $p['action'],
                    'payload' => $payload,
                    'confidence' => 0.85,
                ];
            }
        }

        return ['action' => null, 'payload' => null];
    }

    protected function containsDangerousPatterns(string $message): bool
    {
        foreach ($this->dangerousPatterns as $pattern) {
            if (preg_match($pattern, $message)) {
                return true;
            }
        }
        return false;
    }

    public function requiresConfirmation(string $action): bool
    {
        return in_array($action, self::CONFIRM_REQUIRED_ACTIONS);
    }

    public function isWhitelisted(string $action): bool
    {
        return in_array($action, self::WHITELISTED_ACTIONS);
    }
}
