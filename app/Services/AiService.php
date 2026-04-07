<?php

namespace App\Services;

use App\Models\AiConfig;
use App\Models\ChatSession;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    protected const FREE_MODELS = [
        'meta-llama/llama-3.1-8b-instruct:free',
        'mistralai/mistral-7b-instruct:free',
        'google/gemini-2.0-flash-lite-preview-02-05:free',
        'deepseek/deepseek-chat:free'
    ];
    protected const PAID_FALLBACK = 'google/gemini-2.0-flash-001'; // Paid but reliable

    /**
     * Get the active config for a given purpose.
     */
    public function getActiveConfig(string $purpose): ?AiConfig
    {
        return AiConfig::active()->byPurpose($purpose)->first();
    }

    /**
     * Send a prompt to the configured AI provider.
     */
    protected function callAi(AiConfig $config, string $prompt): array
    {
        try {
            $apiKey = '';
            if (!empty($config->api_key)) {
                try {
                    $apiKey = decrypt($config->api_key);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    $apiKey = $config->api_key; // Fallback to raw string if manually seeded/inserted
                }
            }

            if (empty($apiKey)) {
                return ['error' => "API Key cho cấu hình '{$config->provider}' bị trống."];
            }

            $provider = $config->provider;
            $model = $config->model_name;
            $temperature = (float) ($config->temperature ?? 0.7);
            $maxTokens = (int) ($config->max_tokens ?? 2000);

            if ($provider === 'openrouter') {
                return $this->callOpenRouter($apiKey, $model, $config->default_prompt, $prompt, $temperature, $maxTokens);
            }

            if ($provider === 'groq') {
                return $this->callGroq($apiKey, $model, $config->default_prompt, $prompt, $temperature, $maxTokens);
            }

            if ($provider === 'openai') {
                return $this->callOpenAI($apiKey, $model, $config->default_prompt, $prompt, $temperature, $maxTokens, $config->base_url);
            }

            if ($provider === 'anthropic') {
                return $this->callAnthropic($apiKey, $model, $config->default_prompt, $prompt, $maxTokens, $config->base_url);
            }

            if ($provider === 'google') {
                return $this->callGoogle($apiKey, $model, $config->default_prompt, $prompt, $temperature, $maxTokens);
            }

            return ['error' => "Provider '{$provider}' không được hỗ trợ."];

        } catch (\Exception $e) {
            Log::error('AI Service Error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return ['error' => 'Lỗi khi gọi AI: ' . $e->getMessage()];
        }
    }

    /**
     * Call OpenRouter API (Primary - Recommended).
     */
    public function callOpenRouter(string $apiKey, string $model, ?string $systemPrompt, string $prompt, float $temperature, int $maxTokens): array
    {
        $startTime = microtime(true);
        $maxFreeTime = 10; // 10 seconds for FREE
        $lastError = '';

        // 1. Try the specifically requested model (if active)
        if ($model) {
            Log::info("Trying primary model: {$model}");
            $result = $this->callOpenRouterDirectAction($apiKey, $model, $systemPrompt, $prompt, $temperature, $maxTokens, 15);
            if ($result['success']) return ['content' => $result['content']];
            $lastError = $result['error'];
        }

        // 2. If primary failed or was not specified, and we are within 10s window, try FREE models
        if ((microtime(true) - $startTime) < $maxFreeTime) {
            foreach (self::FREE_MODELS as $freeModel) {
                if ($freeModel === $model) continue;

                Log::info("Trying fallback FREE model: {$freeModel} (Elapsed: " . round(microtime(true) - $startTime) . "s)");
                $result = $this->callOpenRouterDirectAction($apiKey, $freeModel, $systemPrompt, $prompt, $temperature, $maxTokens, 10);
                if ($result['success']) return ['content' => $result['content']];
                
                $lastError = $result['error'];
                if (str_contains($lastError, '401') || str_contains($lastError, 'API Key')) break;
            }
        }

        // 3. Final Fallback to PAID model after 10s
        Log::warning("All attempts failed after 10s. Using PAID fallback: " . self::PAID_FALLBACK);
        $result = $this->callOpenRouterDirectAction($apiKey, self::PAID_FALLBACK, $systemPrompt, $prompt, $temperature, $maxTokens, 30);
        
        if ($result['success']) {
            return ['content' => $result['content'], 'billed' => true];
        }

        return ['error' => "Đã thử cả AI Free (10s) và AI Trả phí đều thất bại. Lỗi cuối: {$lastError}"];
    }

    protected function callOpenRouterDirectAction(string $apiKey, string $model, ?string $systemPrompt, string $prompt, float $temperature, int $maxTokens, int $timeout): array
    {
        $url = 'https://openrouter.ai/api/v1/chat/completions';
        try {
            $messages = [];
            if ($systemPrompt) {
                $messages[] = ['role' => 'system', 'content' => $systemPrompt];
            }
            $messages[] = ['role' => 'user', 'content' => $prompt];

            $response = Http::withoutVerifying()
                ->timeout($timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'HTTP-Referer' => config('app.url', 'http://localhost'),
                    'X-Title' => config('app.name', 'AI Quiz System'),
                ])
                ->post($url, [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => $temperature,
                    'max_tokens' => $maxTokens,
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content', '');
                return ['success' => true, 'content' => $content];
            }

            return ['success' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Call Groq API (Fallback - Free tier available).
     */
    public function callGroq(string $apiKey, string $model, ?string $systemPrompt, string $prompt, float $temperature, int $maxTokens): array
    {
        $url = 'https://api.groq.com/openai/v1/chat/completions';

        try {
            $messages = [];
            if ($systemPrompt) {
                $messages[] = ['role' => 'system', 'content' => $systemPrompt];
            }
            $messages[] = ['role' => 'user', 'content' => $prompt];

            $response = Http::withoutVerifying()
                ->timeout(120)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($url, [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => $temperature,
                    'max_tokens' => $maxTokens,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';
                
                if (empty($content)) {
                    return ['error' => 'Groq trả về phản hồi trống.'];
                }
                
                return ['content' => $content];
            }

            $errorBody = $response->json();
            $errorMessage = $errorBody['error']['message'] ?? $response->body();
            
            Log::error('Groq API Error', [
                'status' => $response->status(),
                'body' => $errorBody,
            ]);

            return ['error' => 'Groq lỗi: ' . $errorMessage];

        } catch (\Exception $e) {
            Log::error('Groq Exception', ['message' => $e->getMessage()]);
            return ['error' => 'Lỗi kết nối Groq: ' . $e->getMessage()];
        }
    }

    /**
     * Call OpenAI API.
     */
    protected function callOpenAI(string $apiKey, string $model, ?string $systemPrompt, string $prompt, float $temperature, int $maxTokens, ?string $baseUrl): array
    {
        $url = $baseUrl 
            ? rtrim($baseUrl, '/') . '/chat/completions'
            : 'https://api.openai.com/v1/chat/completions';

        try {
            $messages = [];
            if ($systemPrompt) {
                $messages[] = ['role' => 'system', 'content' => $systemPrompt];
            }
            $messages[] = ['role' => 'user', 'content' => $prompt];

            $response = Http::withoutVerifying()
                ->withToken($apiKey)
                ->timeout(120)
                ->post($url, [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => $temperature,
                    'max_tokens' => $maxTokens,
                ]);

            if ($response->successful()) {
                return ['content' => $response->json('choices.0.message.content', '')];
            }

            return ['error' => 'OpenAI API error: ' . $response->body()];

        } catch (\Exception $e) {
            Log::error('OpenAI Exception', ['message' => $e->getMessage()]);
            return ['error' => 'Lỗi kết nối OpenAI: ' . $e->getMessage()];
        }
    }

    /**
     * Call Google AI API.
     */
    protected function callGoogle(string $apiKey, string $model, ?string $systemPrompt, string $prompt, float $temperature, int $maxTokens): array
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        try {
            $fullPrompt = ($systemPrompt ? $systemPrompt . "\n\n" : '') . $prompt;

            $response = Http::withoutVerifying()->timeout(120)->post($url, [
                'contents' => [
                    ['parts' => [['text' => $fullPrompt]]],
                ],
                'generationConfig' => [
                    'temperature' => $temperature,
                    'maxOutputTokens' => $maxTokens,
                ],
            ]);

            if ($response->successful()) {
                $content = $response->json('candidates.0.content.parts.0.text', '');
                if (empty($content)) {
                    return ['error' => 'Google AI trả về phản hồi trống.'];
                }
                return ['content' => $content];
            }

            return ['error' => 'Google AI API error: ' . $response->body()];

        } catch (\Exception $e) {
            Log::error('Google AI Exception', ['message' => $e->getMessage()]);
            return ['error' => 'Lỗi kết nối Google AI: ' . $e->getMessage()];
        }
    }

    /**
     * Call Anthropic API.
     */
    protected function callAnthropic(string $apiKey, string $model, ?string $systemPrompt, string $prompt, int $maxTokens, ?string $baseUrl): array
    {
        $url = $baseUrl 
            ? rtrim($baseUrl, '/') . '/v1/messages'
            : 'https://api.anthropic.com/v1/messages';

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(120)->post($url, [
                'model' => $model,
                'max_tokens' => $maxTokens,
                'system' => $systemPrompt ?? 'You are a helpful educational assistant.',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            if ($response->successful()) {
                $content = $response->json('content.0.text', '');
                if (empty($content)) {
                    return ['error' => 'Anthropic trả về phản hồi trống.'];
                }
                return ['content' => $content];
            }

            return ['error' => 'Anthropic API error: ' . $response->body()];

        } catch (\Exception $e) {
            Log::error('Anthropic Exception', ['message' => $e->getMessage()]);
            return ['error' => 'Lỗi kết nối Anthropic: ' . $e->getMessage()];
        }
    }

    /**
     * Generate questions from AI.
     */
    public function generateQuestions(array $payload): array
    {
        $config = $this->getActiveConfig(AiConfig::PURPOSE_QUESTION_GENERATION);

        if (!$config) {
            // Fallback to direct API call if no DB config is active
            Log::info('No active DB config for question generation, falling back to direct API call.');
            // We need to build the prompt before calling directApiCall
            $prompt = "Tạo {$payload['number']} câu hỏi trắc nghiệm về chủ đề: {$payload['topic']}.\n";
            $prompt .= "Loại: {$payload['type']}, Độ khó: {$payload['difficulty']}.\n";
            if (!empty($payload['prompt'])) $prompt .= "Yêu cầu bổ sung: {$payload['prompt']}\n";
            if (!empty($payload['document'])) $prompt .= "\nNội dung tài liệu tham khảo:\n" . substr($payload['document'], 0, 5000) . "\n";
            $prompt .= "\nTrả về JSON array, mỗi phần tử có format:\n";
            $prompt .= '[{"content": "Nội dung câu hỏi", "explanation": "Giải thích ngắn gọn tại sao đáp án đúng là đúng (tối đa 200 ký tự)", "answers": [{"option_text": "Đáp án A", "is_correct": false}, ...]}]';
            
            $result = $this->directApiCall($prompt);
            if (isset($result['error'])) return $result;
            
            $parsedQuestions = $this->parseAiQuestions($result['content']);
            if (empty($parsedQuestions)) return ['error' => 'Không thể phân tích câu hỏi từ phản hồi AI.'];
            return ['content' => $parsedQuestions];
        }

        // Build prompt
        $prompt = "Tạo {$payload['number']} câu hỏi trắc nghiệm về chủ đề: {$payload['topic']}.\n";
        $prompt .= "Loại: {$payload['type']}, Độ khó: {$payload['difficulty']}.\n";

        if (!empty($payload['prompt'])) {
            $prompt .= "Yêu cầu bổ sung: {$payload['prompt']}\n";
        }

        if (!empty($payload['document'])) {
            $prompt .= "\nNội dung tài liệu tham khảo:\n" . substr($payload['document'], 0, 5000) . "\n";
        }

        $prompt .= "\nTrả về JSON array, mỗi phần tử có format:\n";
        $prompt .= '[{"content": "Nội dung câu hỏi", "explanation": "Giải thích ngắn gọn tại sao đáp án đúng là đúng (tối đa 200 ký tự)", "answers": [{"option_text": "Đáp án A", "is_correct": false}, ...]}]';

        // Try the configured AI first
        $result = $this->callAi($config, $prompt);

        if (isset($result['error'])) {
            // DEMO FALLBACK: If no API key is available, generate mock questions for testing purposes
            if (empty(env('OPENROUTER_API_KEY')) && empty(env('GROQ_API_KEY'))) {
                $requestedNum = (int)$payload['number'];
                $mockData = [];
                
                $templates = [
                    "Theo mock data, đâu là khái niệm cốt lõi số {id} của framework Laravel?",
                    "Trong lập trình PHP, làm sao để thực thi nghiệp vụ số {id} này?",
                    "Để tối ưu hoá ứng dụng, phương pháp số {id} nào hiệu quả nhất?",
                    "Chức năng nào đại diện cho quy trình số {id} trong kiến trúc MVC?"
                ];

                for ($i = 0; $i < $requestedNum; $i++) {
                    $template = $templates[$i % count($templates)];
                    $content = str_replace('{id}', $i + 1, $template);

                    $mockData[] = [
                        'content' => "[DEMO] " . $content,
                        'answers' => [
                            ['option_text' => 'Phương án đúng (Đáp án A)', 'is_correct' => true],
                            ['option_text' => 'Phương án sai (Đáp án B)', 'is_correct' => false],
                            ['option_text' => 'Phương án sai (Đáp án C)', 'is_correct' => false],
                            ['option_text' => 'Phương án sai (Đáp án D)', 'is_correct' => false]
                        ]
                    ];
                }
                
                return ['content' => $mockData];
            }

            Log::warning('AI Config failed, trying fallback', [
                'purpose' => AiConfig::PURPOSE_QUESTION_GENERATION,
                'error' => $result['error'],
            ]);
            $fallback = $this->directApiCall($prompt);
            if (!isset($fallback['error'])) {
                $result = $fallback;
            } else {
                return $fallback;
            }
        }

        // result['content'] is a string from AI here
        $parsedQuestions = $this->parseAiQuestions($result['content']);

        if (empty($parsedQuestions)) {
            return ['error' => 'Không thể phân tích câu hỏi từ phản hồi AI.'];
        }

        return ['content' => $parsedQuestions];
    }

    public function parseAiQuestions(string $content): array
    {
        $content = trim($content);

        // More robust JSON extraction: find the first '[' and last ']'
        $firstBracket = strpos($content, '[');
        $lastBracket = strrpos($content, ']');

        if ($firstBracket !== false && $lastBracket !== false && $lastBracket > $firstBracket) {
            $jsonContent = substr($content, $firstBracket, $lastBracket - $firstBracket + 1);
            $data = json_decode($jsonContent, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                return $this->normalizeParsedQuestions($data);
            }
        }

        // Fallback for markdown-wrapped JSON if above simple check fails for some reason
        if (preg_match('/```json\s*(.*?)\s*```/s', $content, $matches)) {
            $data = json_decode(trim($matches[1]), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                return $this->normalizeParsedQuestions($data);
            }
        }

        Log::error('Failed to parse AI JSON', [
            'content_start' => substr($content, 0, 500),
            'json_error' => json_last_error_msg()
        ]);

        return $this->parseTextBasedQuestions($content);
    }

    protected function normalizeParsedQuestions(array $data): array
    {
        $questions = [];

        foreach ($data as $item) {
            // Flexible content key checking
            $content = $item['content'] ?? $item['question'] ?? $item['text'] ?? null;
            $answersData = $item['answers'] ?? $item['options'] ?? $item['choices'] ?? null;

            if (!$content || !$answersData || !is_array($answersData)) {
                continue;
            }

            $answers = [];
            foreach ($answersData as $ans) {
                $answers[] = [
                    'option_text' => $ans['option_text'] ?? $ans['text'] ?? $ans['option'] ?? $ans['value'] ?? '',
                    'is_correct' => $ans['is_correct'] ?? $ans['correct'] ?? $ans['isCorrect'] ?? false,
                ];
            }

            if (count($answers) < 2) {
                continue;
            }

            $explanation = $item['explanation'] ?? $item['explain'] ?? $item['reason'] ?? '';

            $questions[] = [
                'content' => trim($content),
                'explanation' => trim($explanation),
                'answers' => $answers,
            ];
        }

        return $questions;
    }

    protected function parseTextBasedQuestions(string $content): array
    {
        $questions = [];
        $blocks = preg_split('/\n(?=\d+\.|\-|\*\*)/', $content);

        foreach ($blocks as $block) {
            $block = trim($block);
            if (empty($block)) continue;

            if (preg_match('/^\d+\.\s*(.+)/', $block, $qMatch)) {
                $questionContent = trim($qMatch[1]);
                $answers = [];

                if (preg_match_all('/([A-D])\.\s*(.+?)(?=\n[A-D]\.|\n$|$)/is', $block, $ansMatches, PREG_SET_ORDER)) {
                    foreach ($ansMatches as $m) {
                        $isCorrect = preg_match('/\*/', $m[2]) || preg_match('/\(đúng\)|\(correct\)/i', $m[2]);
                        $text = preg_replace('/[\*\(\)đúng\s\(\)correct]+/', '', $m[2]);
                        $answers[] = [
                            'option_text' => trim($m[1] . '. ' . $text),
                            'is_correct' => $isCorrect,
                        ];
                    }
                }

                if (count($answers) >= 2) {
                    $questions[] = [
                        'content' => $questionContent,
                        'answers' => $answers,
                    ];
                }
            }
        }

        return $questions;
    }

    /**
     * Evaluate exam result with AI.
     */
    public function evaluateResult(array $payload): array
    {
        $config = $this->getActiveConfig(AiConfig::PURPOSE_RESULT_EVALUATION);

        if (!$config) {
            Log::info('No active DB config for result evaluation, falling back to direct API call.');
            $prompt = "Đánh giá kết quả thi của học sinh:\n";
            $prompt .= "Tên: {$payload['student_name']}\n";
            $prompt .= "Bài thi: {$payload['exam_title']} (Chủ đề: {$payload['topic_name']})\n";
            $prompt .= "Điểm: {$payload['score_pct']}% ({$payload['correct_count']}/{$payload['total_questions']} đúng)\n";
            $prompt .= "Kết quả: " . ($payload['passed'] ? 'Đạt' : 'Không đạt') . "\n";
            if (!empty($payload['weak_topics'])) $prompt .= "Chủ đề yếu: " . implode(', ', $payload['weak_topics']) . "\n";
            if (!empty($payload['strong_topics'])) $prompt .= "Chủ đề mạnh: " . implode(', ', $payload['strong_topics']) . "\n";
            $prompt .= "\nTrả về JSON:\n{\"summary\": \"Nhận xét tổng quát\", \"strengths\": [\"...\"], \"weaknesses\": [\"...\"], \"suggestions\": [\"...\"]}";
            
            return $this->directApiCall($prompt);
        }

        $prompt = "Đánh giá kết quả thi của học sinh:\n";
        $prompt .= "Tên: {$payload['student_name']}\n";
        $prompt .= "Bài thi: {$payload['exam_title']} (Chủ đề: {$payload['topic_name']})\n";
        $prompt .= "Điểm: {$payload['score_pct']}% ({$payload['correct_count']}/{$payload['total_questions']} đúng)\n";
        $prompt .= "Kết quả: " . ($payload['passed'] ? 'Đạt' : 'Không đạt') . "\n";

        if (!empty($payload['weak_topics'])) {
            $prompt .= "Chủ đề yếu: " . implode(', ', $payload['weak_topics']) . "\n";
        }
        if (!empty($payload['strong_topics'])) {
            $prompt .= "Chủ đề mạnh: " . implode(', ', $payload['strong_topics']) . "\n";
        }

        $prompt .= "\nTrả về JSON:\n{\"summary\": \"Nhận xét tổng quát\", \"strengths\": [\"...\"], \"weaknesses\": [\"...\"], \"suggestions\": [\"...\"]}";

        $result = $this->callAi($config, $prompt);

        if (isset($result['error'])) {
            Log::warning('AI Config failed, trying fallback', [
                'purpose' => AiConfig::PURPOSE_RESULT_EVALUATION,
                'error' => $result['error'],
            ]);
            $fallback = $this->directApiCall($prompt);
            if (!isset($fallback['error'])) {
                return $fallback;
            }
        }

        return $result;
    }

    /**
     * Explain an answer with AI.
     */
    public function explainAnswer(array $payload): array
    {
        $config = $this->getActiveConfig(AiConfig::PURPOSE_ANSWER_EXPLANATION);

        if (!$config) {
            Log::info('No active DB config for answer explanation, falling back to direct API call.');
            $prompt = "Giải thích chi tiết cho câu hỏi trắc nghiệm:\n";
            $prompt .= "Câu hỏi: {$payload['question']}\n";
            $prompt .= "Đáp án đúng: {$payload['correct_answer']}\n";
            $prompt .= "Câu trả lời của học sinh: {$payload['student_answer']}\n";
            $prompt .= "Kết quả: " . ($payload['is_correct'] ? 'Đúng' : 'Sai') . "\n\n";
            $prompt .= "Hãy giải thích tại sao đáp án đúng là đúng và giúp học sinh hiểu rõ hơn.";
            
            return $this->directApiCall($prompt);
        }

        $prompt = "Giải thích chi tiết cho câu hỏi trắc nghiệm:\n";
        $prompt .= "Câu hỏi: {$payload['question']}\n";
        $prompt .= "Đáp án đúng: {$payload['correct_answer']}\n";
        $prompt .= "Câu trả lời của học sinh: {$payload['student_answer']}\n";
        $prompt .= "Kết quả: " . ($payload['is_correct'] ? 'Đúng' : 'Sai') . "\n\n";
        $prompt .= "Hãy giải thích tại sao đáp án đúng là đúng và giúp học sinh hiểu rõ hơn.";

        $result = $this->callAi($config, $prompt);

        if (isset($result['error'])) {
            Log::warning('AI Config failed, trying fallback', [
                'purpose' => AiConfig::PURPOSE_ANSWER_EXPLANATION,
                'error' => $result['error'],
            ]);
            $fallback = $this->directApiCall($prompt);
            if (!isset($fallback['error'])) {
                return $fallback;
            }
        }

        return $result;
    }
    /**
     * Explain an answer with AI in depth for pedagogical purposes.
     */
    public function explainDeeper(array $payload): array
    {
        $config = $this->getActiveConfig(AiConfig::PURPOSE_ANSWER_EXPLANATION);

        $prompt = "Hãy đóng vai một giáo viên tận tâm, giải thích sâu hơn về kiến thức liên quan đến câu hỏi sau:\n";
        $prompt .= "Câu hỏi: {$payload['question']}\n";
        $prompt .= "Đáp án đúng: {$payload['correct_answer']}\n";
        if (!empty($payload['current_explanation'])) {
            $prompt .= "Giải thích hiện tại: {$payload['current_explanation']}\n";
        }
        $prompt .= "\nYêu cầu:\n";
        $prompt .= "1. Phân tích bản chất kiến thức đằng sau câu hỏi.\n";
        $prompt .= "2. Tại sao đáp án đúng là lựa chọn chính xác nhất.\n";
        $prompt .= "3. Tại sao các phương án khác chưa đúng (với các lỗi sai phổ biến).\n";
        $prompt .= "4. Đưa ra 1 ví dụ minh họa thực tế hoặc mẹo ghi nhớ nhanh.\n";
        $prompt .= "Hãy viết bằng giọng văn khích lệ, dễ hiểu.";

        if ($config) {
            $result = $this->callAi($config, $prompt);
            if (!isset($result['error'])) return $result;
        }

        return $this->directApiCall($prompt);
    }

    /**
     * Generate a learning path based on result.
     */
    public function generateLearningPath(array $payload): array
    {
        $config = $this->getActiveConfig(AiConfig::PURPOSE_LEARNING_PATH);

        if (!$config) {
            Log::info('No active DB config for learning path, falling back to direct API call.');
            $prompt = "Tạo lộ trình học tập cho học sinh:\n";
            $prompt .= "Tên: {$payload['student_name']}\n";
            $prompt .= "Bài thi: {$payload['exam_title']} (Chủ đề: {$payload['topic_name']})\n";
            $prompt .= "Điểm: {$payload['score_pct']}%\n";
            if (!empty($payload['weak_topics'])) $prompt .= "Chủ đề cần cải thiện: " . implode(', ', $payload['weak_topics']) . "\n";
            $prompt .= "\nTrả về JSON:\n";
            $prompt .= '{"overall_goal": "Mục tiêu tổng", "weekly_plan": [{"week": 1, "focus": "Nội dung", "activities": ["..."], "estimated_time": "..."}], "recommended_resources": ["..."], "tips": ["..."]}';
            
            return $this->directApiCall($prompt);
        }

        $prompt = "Tạo lộ trình học tập cho học sinh:\n";
        $prompt .= "Tên: {$payload['student_name']}\n";
        $prompt .= "Bài thi: {$payload['exam_title']} (Chủ đề: {$payload['topic_name']})\n";
        $prompt .= "Điểm: {$payload['score_pct']}%\n";

        if (!empty($payload['weak_topics'])) {
            $prompt .= "Chủ đề cần cải thiện: " . implode(', ', $payload['weak_topics']) . "\n";
        }

        $prompt .= "\nTrả về JSON:\n";
        $prompt .= '{"overall_goal": "Mục tiêu tổng", "weekly_plan": [{"week": 1, "focus": "Nội dung", "activities": ["..."], "estimated_time": "..."}], "recommended_resources": ["..."], "tips": ["..."]}';

        $result = $this->callAi($config, $prompt);

        if (isset($result['error'])) {
            Log::warning('AI Config failed, trying fallback', [
                'purpose' => AiConfig::PURPOSE_LEARNING_PATH,
                'error' => $result['error'],
            ]);
            $fallback = $this->directApiCall($prompt);
            if (!isset($fallback['error'])) {
                return $fallback;
            }
        }

        return $result;
    }

    /**
     * Generate AI response for chat.
     */
    public function generateResponse(ChatSession $session, string $userMessage): array
    {
        $config = $this->getActiveConfig(AiConfig::PURPOSE_GENERAL);

        // Get chat history for context
        $messages = $session->messages()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->reverse()
            ->values();

        $context = '';
        foreach ($messages as $msg) {
            $role = $msg->sender_type === 'user' ? 'user' : 'assistant';
            $context .= "\n{$role}: " . $msg->content;
        }

        $fullPrompt = "Cuộc trò chuyện trước đó:{$context}\n\nuser: {$userMessage}\n\nHãy trả lời dựa trên ngữ cảnh cuộc trò chuyện.";

        // Try AI config first
        if ($config) {
            $result = $this->callAi($config, $fullPrompt);

            if (!isset($result['error'])) {
                return $result;
            }

            Log::warning('AI Config failed for chat, trying fallback', [
                'purpose' => AiConfig::PURPOSE_GENERAL,
                'error' => $result['error'],
            ]);
        }

        // Fallback to env-based API
        return $this->directApiCall($fullPrompt);
    }

    /**
     * Direct API call using env variables (fallback).
     */
    protected function directApiCall(string $message): array
    {
        $apiKey = env('OPENROUTER_API_KEY');
        $model = env('OPENROUTER_MODEL', 'meta-llama/llama-3.1-8b-instruct:free');
        
        if (!$apiKey || $apiKey === 'your-openrouter-api-key-here') {
            return ['error' => 'Chưa cấu hình API Key cho OpenRouter.'];
        }

        $result = $this->callOpenRouter($apiKey, $model, null, $message, 0.7, 2000);
        
        if (isset($result['error'])) {
            return ['success' => false, 'error' => $result['error']];
        }

        return ['success' => true, 'content' => $result['content'], 'billed' => $result['billed'] ?? false];
    }

    protected function callGroqDirect(string $apiKey, string $model, string $message): array
    {
        try {
            $response = Http::withoutVerifying()
                ->timeout(120)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [['role' => 'user', 'content' => $message]],
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content', '');
                return ['success' => true, 'content' => $content];
            }

            return ['success' => false, 'error' => 'Groq error: ' . $response->body()];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
