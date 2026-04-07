<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\AiChatCommand;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminAgentService
{
    protected AdminActionParser $parser;
    protected AdminActionExecutor $executor;

    public function __construct()
    {
        $this->parser = new AdminActionParser();
        $this->executor = new AdminActionExecutor();
    }

    public function processCommand(string $message, ?int $userId = null): array
    {
        $userId = $userId ?? Auth::id();
        $this->executor->setUserId($userId);

        $command = AiChatCommand::create([
            'user_id' => $userId,
            'message' => $message,
            'status' => AiChatCommand::STATUS_PENDING,
            'requires_confirmation' => false,
        ]);

        $parseResult = $this->parser->parse($message);

        if (isset($parseResult['blocked']) && $parseResult['blocked']) {
            $command->update([
                'status' => AiChatCommand::STATUS_BLOCKED,
                'result_message' => $parseResult['error'] ?? 'Lệnh bị chặn.',
            ]);
            $this->logActivity($userId, $message, null, $parseResult['error'] ?? 'Lệnh bị chặn', 'blocked');
            return [
                'success' => false,
                'command_id' => $command->id,
                'status' => AiChatCommand::STATUS_BLOCKED,
                'message' => $parseResult['error'] ?? 'Lệnh bị chặn.',
                'parsed_action' => null,
                'parsed_payload' => null,
                'requires_confirmation' => false,
            ];
        }

        if (isset($parseResult['error'])) {
            $command->update([
                'status' => AiChatCommand::STATUS_FAILED,
                'result_message' => $parseResult['error'],
            ]);
            return [
                'success' => false,
                'command_id' => $command->id,
                'status' => AiChatCommand::STATUS_FAILED,
                'message' => $parseResult['error'],
                'parsed_action' => null,
                'parsed_payload' => null,
                'requires_confirmation' => false,
            ];
        }

        if (empty($parseResult['action'])) {
            $command->update([
                'status' => AiChatCommand::STATUS_FAILED,
                'result_message' => $parseResult['error'] ?? 'Không thể hiểu lệnh.',
            ]);
            return [
                'success' => false,
                'command_id' => $command->id,
                'status' => AiChatCommand::STATUS_FAILED,
                'message' => $parseResult['error'] ?? 'Không thể hiểu lệnh.',
                'parsed_action' => null,
                'parsed_payload' => null,
                'requires_confirmation' => false,
            ];
        }

        $action = $parseResult['action'];
        $payload = $parseResult['payload'] ?? [];
        $requiresConfirmation = $this->parser->requiresConfirmation($action);

        $command->update([
            'parsed_action' => $action,
            'parsed_payload' => $payload,
            'status' => AiChatCommand::STATUS_PARSED,
            'requires_confirmation' => $requiresConfirmation,
        ]);

        if ($requiresConfirmation) {
            return [
                'success' => true,
                'command_id' => $command->id,
                'status' => AiChatCommand::STATUS_PARSED,
                'message' => 'Lệnh đã được phân tích. Cần xác nhận trước khi thực thi.',
                'parsed_action' => $action,
                'parsed_payload' => $payload,
                'requires_confirmation' => true,
                'confirmation_message' => $this->getConfirmationMessage($action, $payload),
            ];
        }

        $result = $this->executor->execute($action, $payload);

        if ($result['success']) {
            $command->update([
                'status' => AiChatCommand::STATUS_EXECUTED,
                'result_message' => $result['message'],
                'executed_at' => now(),
            ]);
            $this->logActivity($userId, $message, $action, $result['message'], 'executed');
        } else {
            $command->update([
                'status' => AiChatCommand::STATUS_FAILED,
                'result_message' => $result['message'],
            ]);
            $this->logActivity($userId, $message, $action, $result['message'], 'failed');
        }

        return [
            'success' => $result['success'],
            'command_id' => $command->id,
            'status' => $result['success'] ? AiChatCommand::STATUS_EXECUTED : AiChatCommand::STATUS_FAILED,
            'message' => $result['message'],
            'parsed_action' => $action,
            'parsed_payload' => $payload,
            'requires_confirmation' => false,
        ];
    }

    public function confirmCommand(int $commandId): array
    {
        $command = AiChatCommand::findOrFail($commandId);

        if (!$command->requires_confirmation) {
            return [
                'success' => false,
                'message' => 'Lệnh này không cần xác nhận.',
            ];
        }

        if ($command->confirmed_at) {
            return [
                'success' => false,
                'message' => 'Lệnh đã được xác nhận trước đó.',
            ];
        }

        $command->update([
            'status' => AiChatCommand::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);

        $this->executor->setUserId($command->user_id);
        $result = $this->executor->execute($command->parsed_action, $command->parsed_payload ?? []);

        if ($result['success']) {
            $command->update([
                'status' => AiChatCommand::STATUS_EXECUTED,
                'result_message' => $result['message'],
                'executed_at' => now(),
            ]);
            $this->logActivity($command->user_id, $command->message, $command->parsed_action, $result['message'], 'executed');
        } else {
            $command->update([
                'status' => AiChatCommand::STATUS_FAILED,
                'result_message' => $result['message'],
            ]);
            $this->logActivity($command->user_id, $command->message, $command->parsed_action, $result['message'], 'failed');
        }

        return [
            'success' => $result['success'],
            'command_id' => $command->id,
            'status' => $result['success'] ? AiChatCommand::STATUS_EXECUTED : AiChatCommand::STATUS_FAILED,
            'message' => $result['message'],
        ];
    }

    protected function getConfirmationMessage(string $action, array $payload): string
    {
        $warnings = [
            'delete_topic' => "Bạn đang yêu cầu xóa chủ đề. Hành động này không thể hoàn tác.",
            'delete_question' => "Bạn đang yêu cầu xóa câu hỏi. Hành động này không thể hoàn tác.",
            'delete_exam' => "Bạn đang yêu cầu xóa bài thi. Hành động này không thể hoàn tác.",
            'delete_document' => "Bạn đang yêu cầu xóa tài liệu. Hành động này không thể hoàn tác.",
            'lock_user' => "Bạn đang yêu cầu khóa tài khoản người dùng.",
            'unlock_user' => "Bạn đang yêu cầu mở khóa tài khoản người dùng.",
            'update_ai_config' => "Bạn đang yêu cầu cập nhật cấu hình AI.",
            'toggle_ai_config' => "Bạn đang yêu cầu bật/tắt cấu hình AI.",
            'create_ai_config' => "Bạn đang yêu cầu tạo cấu hình AI mới.",
        ];

        $warning = $warnings[$action] ?? 'Hành động này cần xác nhận trước khi thực thi.';

        if (!empty($payload['entity_name'])) {
            $warning .= " Mục tiêu: {$payload['entity_name']}";
        }

        return $warning . " Bạn có chắc chắn muốn ti��p tục?";
    }

    protected function logActivity(int $userId, string $message, ?string $action, ?string $result, string $status): void
    {
        $description = "AI Agent: {$message}\nKết quả: {$result}";

        ActivityLogger::log(
            "ai_agent_" . $status,
            'ai_chat_commands',
            null,
            $description
        );
    }

    public function getCommandHistory(int $userId, int $perPage = 20): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return AiChatCommand::byUser($userId)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function getCommandById(int $commandId): ?AiChatCommand
    {
        return AiChatCommand::find($commandId);
    }
}
