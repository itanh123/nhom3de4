<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ChatManagementController extends Controller
{
    /**
     * Display all chat sessions (admin view).
     */
    public function index(Request $request)
    {
        $query = ChatSession::with(['user', 'latestMessage'])
            ->userSessions(Auth::id())
            ->recent();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('starred')) {
            $query->starred();
        }

        $sessions = $query->paginate(20);

        return view('admin.chat.index', compact('sessions'));
    }

    /**
     * Display a specific chat session with all messages.
     */
    public function show(ChatSession $session)
    {
        // Only allow admin to view their own sessions
        abort_unless($session->user_id === Auth::id() || Auth::user()->isAdmin(), 403);

        $session->load(['user', 'messages' => function ($q) {
            $q->orderBy('created_at', 'asc');
        }]);

        // Mark messages as read
        $session->messages()
            ->where('sender_type', '!=', 'admin')
            ->where('status', '!=', 'read')
            ->update(['status' => 'read']);

        return view('admin.chat.show', compact('session'));
    }

    /**
     * Start a new chat session as admin.
     */
    public function create()
    {
        $users = User::where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get();

        return view('admin.chat.create', compact('users'));
    }

    /**
     * Store a new chat session.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'nullable|string|max:255',
            'first_message' => 'nullable|string|max:2000',
        ]);

        $user = User::findOrFail($request->user_id);

        DB::beginTransaction();
        try {
            $session = ChatSession::create([
                'user_id' => $user->id,
                'title' => $request->title ?? 'Cuộc trò chuyện mới',
                'type' => ChatSession::TYPE_USER,
                'context' => null,
                'is_starred' => false,
                'last_message_at' => now(),
            ]);

            if ($request->filled('first_message')) {
                ChatMessage::create([
                    'chat_session_id' => $session->id,
                    'sender_id' => Auth::id(),
                    'sender_type' => ChatMessage::SENDER_ADMIN,
                    'content' => $request->first_message,
                    'status' => ChatMessage::STATUS_READ,
                ]);

                $session->update(['last_message_at' => now()]);
            }

            // Activity logging
            if (class_exists(ActivityLogger::class) && method_exists(ActivityLogger::class, 'createChatSession')) {
                try {
                    ActivityLogger::createChatSession($session);
                } catch (\Exception $e) {
                    // Silently ignore
                }
            }

            DB::commit();

            return redirect()->route('admin.chat.show', $session)
                ->with('success', 'Đã tạo cuộc trò chuyện với ' . $user->name);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Create Chat Session Error', ['message' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    /**
     * Update chat session (title, starred).
     */
    public function update(Request $request, ChatSession $session)
    {
        abort_unless($session->user_id === Auth::id() || Auth::user()->isAdmin(), 403);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'is_starred' => 'nullable|boolean',
        ]);

        $session->update([
            'title' => $request->title ?? $session->title,
            'is_starred' => $request->boolean('is_starred', $session->is_starred),
        ]);

        return back()->with('success', 'Đã cập nhật cuộc trò chuyện');
    }

    /**
     * Delete a chat session.
     */
    public function destroy(ChatSession $session)
    {
        abort_unless($session->user_id === Auth::id() || Auth::user()->isAdmin(), 403);

        $sessionTitle = $session->title ?? 'Cuộc trò chuyện';
        $session->delete();

        return redirect()->route('admin.chat.index')
            ->with('success', "Đã xóa cuộc trò chuyện: {$sessionTitle}");
    }

    /**
     * Send a message in a chat session.
     */
    public function sendMessage(Request $request, ChatSession $session)
    {
        abort_unless($session->user_id === Auth::id() || Auth::user()->isAdmin(), 403);

        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:1|max:5000',
        ], [
            'message.required' => 'Vui lòng nhập tin nhắn.',
            'message.min' => 'Tin nhắn không được để trống.',
            'message.max' => 'Tin nhắn quá dài (tối đa 5000 ký tự).',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()->first(),
                ], 422);
            }
            return back()->withErrors($validator);
        }

        DB::beginTransaction();
        try {
            // Save admin message
            $message = ChatMessage::create([
                'chat_session_id' => $session->id,
                'sender_id' => Auth::id(),
                'sender_type' => ChatMessage::SENDER_ADMIN,
                'content' => $request->message,
                'status' => ChatMessage::STATUS_SENT,
            ]);

            // Update session last message time
            $session->update(['last_message_at' => now()]);

            // If admin wants AI to respond, generate response
            $aiResponse = null;
            if ($request->boolean('include_ai') && $request->boolean('__ai_trigger')) {
                $aiResponse = $this->generateAiResponse($session, $request->message);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'ai_response' => $aiResponse,
                ]);
            }

            return back();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Send Message Error', ['message' => $e->getMessage()]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Lỗi: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withErrors(['error' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate AI response for the session.
     */
    protected function generateAiResponse(ChatSession $session, string $userMessage): ?ChatMessage
    {
        $aiService = new \App\Services\AiService();
        $result = $aiService->generateResponse($session, $userMessage);

        if (isset($result['error'])) {
            Log::warning('AI Response Error', ['error' => $result['error']]);
            return null;
        }

        return ChatMessage::create([
            'chat_session_id' => $session->id,
            'sender_id' => null,
            'sender_type' => ChatMessage::SENDER_ASSISTANT,
            'content' => $result['content'],
            'status' => ChatMessage::STATUS_SENT,
        ]);
    }

    /**
     * Toggle starred status.
     */
    public function toggleStar(ChatSession $session)
    {
        abort_unless($session->user_id === Auth::id() || Auth::user()->isAdmin(), 403);

        $session->update(['is_starred' => !$session->is_starred]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'is_starred' => $session->is_starred,
            ]);
        }

        return back()->with('success', 'Đã cập nhật trạng thái đánh dấu');
    }

    /**
     * Clear all messages in a session.
     */
    public function clearMessages(ChatSession $session)
    {
        abort_unless($session->user_id === Auth::id() || Auth::user()->isAdmin(), 403);

        $count = $session->messages()->count();
        $session->messages()->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'deleted_count' => $count,
            ]);
        }

        return back()->with('success', "Đã xóa {$count} tin nhắn");
    }

    /**
     * Export chat session as text.
     */
    public function export(ChatSession $session)
    {
        abort_unless($session->user_id === Auth::id() || Auth::user()->isAdmin(), 403);

        $session->load(['user', 'messages' => function ($q) {
            $q->orderBy('created_at', 'asc');
        }]);

        $content = "Cuộc trò chuyện với: " . $session->user->name . "\n";
        $content .= "Email: " . $session->user->email . "\n";
        $content .= "Bắt đầu: " . $session->created_at->format('d/m/Y H:i:s') . "\n";
        $content .= "=" . str_repeat("=", 50) . "\n\n";

        foreach ($session->messages as $msg) {
            $sender = $msg->sender_type === 'admin' ? 'Admin' : ($msg->sender_type === 'assistant' ? 'AI' : $session->user->name);
            $content .= "[{$msg->created_at->format('d/m/Y H:i:s')}] {$sender}:\n";
            $content .= $msg->content . "\n\n";
        }

        $filename = 'chat_' . $session->id . '_' . date('Ymd_His') . '.txt';

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename, ['Content-Type' => 'text/plain']);
    }

    /**
     * Get chat statistics.
     */
    public function stats()
    {
        $totalSessions = ChatSession::count();
        $totalMessages = ChatMessage::count();
        $userCount = ChatSession::distinct('user_id')->count('user_id');
        $starredCount = ChatSession::starred()->count();
        $recentSessions = ChatSession::recent()->limit(5)->get();

        return view('admin.chat.stats', compact(
            'totalSessions',
            'totalMessages',
            'userCount',
            'starredCount',
            'recentSessions'
        ));
    }
}
