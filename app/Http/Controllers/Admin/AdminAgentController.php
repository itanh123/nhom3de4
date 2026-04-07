<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiChatCommand;
use App\Services\AdminAgentService;
use Illuminate\Http\Request;

class AdminAgentController extends Controller
{
    protected AdminAgentService $agentService;

    public function __construct()
    {
        $this->agentService = new AdminAgentService();
    }

    public function index()
    {
        $title = 'AI Agent - Quản lý thông minh';
        
        $recentCommands = AiChatCommand::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.ai_agent.index', compact('title', 'recentCommands'));
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|min:3|max:500',
        ]);

        $result = $this->agentService->processCommand($request->message);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        if ($result['success'] && ($result['requires_confirmation'] ?? false)) {
            return redirect()->back()->with([
                'pending_confirmation' => true,
                'command_id' => $result['command_id'],
                'confirmation_message' => $result['confirmation_message'] ?? 'Cần xác nhận để thực thi lệnh.',
                'parsed_action' => $result['parsed_action'],
                'parsed_payload' => $result['parsed_payload'],
            ]);
        }

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function execute(Request $request)
    {
        $request->validate([
            'command_id' => 'required|integer|exists:ai_chat_commands,id',
        ]);

        $command = AiChatCommand::findOrFail($request->command_id);

        if ($command->user_id !== auth()->id()) {
            abort(403, 'Bạn không có quyền thực thi lệnh này.');
        }

        if ($command->status !== AiChatCommand::STATUS_PARSED) {
            return redirect()->back()->with('error', 'Lệnh không ở trạng thái chờ xác nhận.');
        }

        $result = $this->agentService->confirmCommand($request->command_id);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->route('admin.ai-agent')->with('success', $result['message']);
        }

        return redirect()->route('admin.ai-agent')->with('error', $result['message']);
    }

    public function history(Request $request)
    {
        $title = 'AI Agent - Lịch sử lệnh';
        
        $commands = AiChatCommand::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.ai_agent.history', compact('title', 'commands'));
    }

    public function show(int $id)
    {
        $title = 'AI Agent - Chi tiết lệnh';
        
        $command = AiChatCommand::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('admin.ai_agent.show', compact('title', 'command'));
    }
}
