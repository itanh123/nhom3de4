<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\AiConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    protected int $timeout = 120;
    protected int $maxRetries = 1;

    /**
     * Display the chat UI.
     */
    public function index()
    {
        // Get user's chat sessions
        $sessions = ChatSession::where('user_id', auth()->id())
            ->where('type', ChatSession::TYPE_USER)
            ->recent()
            ->paginate(10);

        return view('chat.index', compact('sessions'));
    }

    /**
     * Handle chat message - OpenRouter primary with Groq fallback.
     */
    public function chat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:1|max:4000',
            'session_id' => 'nullable|exists:chat_sessions,id',
        ], [
            'message.required' => 'Vui lòng nhập tin nhắn.',
            'message.min' => 'Tin nhắn không được để trống.',
            'message.max' => 'Tin nhắn quá dài (tối đa 4000 ký tự).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 422);
        }

        $userMessage = $request->input('message');

        // Get or create session
        $session = $this->getOrCreateSession(auth()->id(), $request->input('session_id'));

        if (!$session) {
            return response()->json([
                'success' => false,
                'error' => 'Không thể tạo phiên trò chuyện.',
            ], 500);
        }

        // Save user message
        $userMsg = ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_id' => auth()->id(),
            'sender_type' => ChatMessage::SENDER_USER,
            'content' => $userMessage,
            'status' => ChatMessage::STATUS_READ,
        ]);

        $session->update(['last_message_at' => now()]);

        // Try OpenRouter first (primary)
        $result = $this->callOpenRouter($userMessage);
        
        // If OpenRouter fails, try Groq fallback
        if (!$result['success'] && $result['provider'] === 'openrouter') {
            Log::warning('OpenRouter failed, attempting Groq fallback', [
                'error' => $result['error'] ?? 'Unknown error'
            ]);
            
            $result = $this->callGroq($userMessage);
        }

        // If both failed, return error
        if (!$result['success']) {
            // Save error as assistant message
            ChatMessage::create([
                'chat_session_id' => $session->id,
                'sender_id' => null,
                'sender_type' => ChatMessage::SENDER_ASSISTANT,
                'content' => 'Xin lỗi, tôi đang gặp sự cố. Vui lòng thử lại sau.',
                'status' => ChatMessage::STATUS_READ,
            ]);

            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Cả OpenRouter và Groq đều không hoạt động. Vui lòng thử lại sau.',
                'session_id' => $session->id,
            ], 500);
        }

        // Save AI response
        $assistantMsg = ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_id' => null,
            'sender_type' => ChatMessage::SENDER_ASSISTANT,
            'content' => $result['reply'],
            'status' => ChatMessage::STATUS_READ,
        ]);

        return response()->json([
            'success' => true,
            'reply' => $result['reply'],
            'provider' => $result['provider'] ?? 'unknown',
            'session_id' => $session->id,
            'message_id' => $assistantMsg->id,
        ]);
    }

    /**
     * Get or create a chat session.
     */
    protected function getOrCreateSession(int $userId, ?int $sessionId = null): ?ChatSession
    {
        try {
            if ($sessionId) {
                $session = ChatSession::where('id', $sessionId)
                    ->where('user_id', $userId)
                    ->first();
                
                if ($session) {
                    return $session;
                }
            }

            // Create new session
            return ChatSession::create([
                'user_id' => $userId,
                'title' => 'Cuộc trò chuyện ' . now()->format('d/m/Y H:i'),
                'type' => ChatSession::TYPE_USER,
                'last_message_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Create Session Error', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get chat history for a session.
     */
    public function history(Request $request, ChatSession $session)
    {
        if ($session->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xem cuộc trò chuyện này.');
        }

        $messages = $session->messages()
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Get all sessions for current user.
     */
    public function sessions()
    {
        $sessions = ChatSession::where('user_id', auth()->id())
            ->where('type', ChatSession::TYPE_USER)
            ->recent()
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'title' => $session->title,
                    'last_message_at' => $session->last_message_at?->diffForHumans(),
                    'is_starred' => $session->is_starred,
                ];
            });

        return response()->json([
            'success' => true,
            'sessions' => $sessions,
        ]);
    }

    /**
     * Delete a session (user).
     */
    public function deleteSession(ChatSession $session)
    {
        if ($session->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền xóa cuộc trò chuyện này.');
        }

        $session->delete();

        return redirect()->route('chat')->with('success', 'Đã xóa cuộc trò chuyện');
    }

    /**
     * Call OpenRouter API.
     */
    protected function callOpenRouter(string $message): array
    {
        $apiKey = env('OPENROUTER_API_KEY');

        if (empty($apiKey) || $apiKey === 'your-openrouter-api-key-here') {
            return [
                'success' => false,
                'provider' => 'openrouter',
                'error' => 'OpenRouter API key chưa được cấu hình.',
            ];
        }

        $model = env('OPENROUTER_MODEL', 'mistralai/mistral-7b-instruct');
        $apiUrl = 'https://openrouter.ai/api/v1/chat/completions';

        try {
            $response = Http::timeout($this->timeout)
                ->withoutVerifying()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                    'HTTP-Referer' => 'http://localhost',
                    'X-Title' => 'AI Chat App',
                ])
                ->post($apiUrl, [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'user', 'content' => $message],
                    ],
                    'stream' => false,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';

                if (empty($content)) {
                    return [
                        'success' => false,
                        'provider' => 'openrouter',
                        'error' => 'Không nhận được phản hồi từ OpenRouter.',
                    ];
                }

                return [
                    'success' => true,
                    'reply' => $content,
                    'provider' => 'openrouter',
                ];
            }

            $errorBody = $response->json();
            $errorMessage = $errorBody['error']['message'] 
                ?? $errorBody['error']['code'] 
                ?? 'Lỗi không xác định từ OpenRouter API';

            Log::error('OpenRouter API Error', [
                'status' => $response->status(),
                'body' => $errorBody,
            ]);

            return [
                'success' => false,
                'provider' => 'openrouter',
                'error' => 'OpenRouter API lỗi: ' . $errorMessage,
                'retryable' => $response->status() >= 500,
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('OpenRouter Connection Error', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'provider' => 'openrouter',
                'error' => 'Không thể kết nối đến OpenRouter. Vui lòng kiểm tra kết nối mạng.',
                'retryable' => true,
            ];

        } catch (\Exception $e) {
            Log::error('OpenRouter Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'provider' => 'openrouter',
                'error' => 'Lỗi OpenRouter: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Call Groq API (fallback).
     */
    protected function callGroq(string $message): array
    {
        $apiKey = env('GROQ_API_KEY');

        if (empty($apiKey) || $apiKey === 'your-groq-api-key-here') {
            return [
                'success' => false,
                'provider' => 'groq',
                'error' => 'Groq API key chưa được cấu hình.',
            ];
        }

        $model = env('GROQ_MODEL', 'llama-3.3-70b-versatile');
        $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';

        try {
            $response = Http::timeout($this->timeout)
                ->withoutVerifying()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $apiKey,
                ])
                ->post($apiUrl, [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'user', 'content' => $message],
                    ],
                    'stream' => false,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';

                if (empty($content)) {
                    return [
                        'success' => false,
                        'provider' => 'groq',
                        'error' => 'Không nhận được phản hồi từ Groq.',
                    ];
                }

                return [
                    'success' => true,
                    'reply' => $content,
                    'provider' => 'groq',
                ];
            }

            $errorBody = $response->json();
            $errorMessage = $errorBody['error']['message'] ?? 'Lỗi không xác định từ Groq API';

            Log::error('Groq API Error', [
                'status' => $response->status(),
                'body' => $errorBody,
            ]);

            return [
                'success' => false,
                'provider' => 'groq',
                'error' => 'Groq API lỗi: ' . $errorMessage,
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Groq Connection Error', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'provider' => 'groq',
                'error' => 'Không thể kết nối đến Groq. Vui lòng kiểm tra kết nối mạng.',
            ];

        } catch (\Exception $e) {
            Log::error('Groq Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'provider' => 'groq',
                'error' => 'Lỗi Groq: ' . $e->getMessage(),
            ];
        }
    }
}
