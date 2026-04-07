<?php

namespace Database\Seeders;

use App\Models\AiConfig;
use App\Models\User;
use Illuminate\Database\Seeder;

class AiConfigSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $apiKey = env('OPENROUTER_API_KEY');

        if (!$apiKey || $apiKey === 'your-openrouter-api-key-here') {
            $this->command->warn('OPENROUTER_API_KEY NOT found in .env. Skipping seeder.');
            return;
        }

        $encryptedKey = encrypt($apiKey);
        $freeModel = 'meta-llama/llama-3.1-8b-instruct:free';

        $configs = [
            [
                'provider' => 'openrouter',
                'model_name' => $freeModel,
                'purpose' => AiConfig::PURPOSE_QUESTION_GENERATION,
                'temperature' => 0.7,
                'max_tokens' => 2000,
                'is_active' => true,
                'default_prompt' => 'Bạn là giáo viên chuyên nghiệp. Hãy tạo câu hỏi trắc nghiệm chất lượng cao dựa trên chủ đề hoặc tài liệu cung cấp.',
            ],
            [
                'provider' => 'openrouter',
                'model_name' => $freeModel,
                'purpose' => AiConfig::PURPOSE_ANSWER_EXPLANATION,
                'temperature' => 0.5,
                'max_tokens' => 1000,
                'is_active' => true,
                'default_prompt' => 'Bạn là giáo viên tận tâm. Hãy giải thích chi tiết tại sao đáp án này là đúng và các phương án khác lại sai.',
            ],
            [
                'provider' => 'openrouter',
                'model_name' => $freeModel,
                'purpose' => AiConfig::PURPOSE_RESULT_EVALUATION,
                'temperature' => 0.6,
                'max_tokens' => 1500,
                'is_active' => true,
                'default_prompt' => 'Bạn là chuyên gia khảo thí. Hãy đánh giá kết quả bài làm của học sinh, nêu bật điểm mạnh/yếu và đưa ra lời khuyên thiết thực.',
            ],
            [
                'provider' => 'openrouter',
                'model_name' => $freeModel,
                'purpose' => AiConfig::PURPOSE_LEARNING_PATH,
                'temperature' => 0.7,
                'max_tokens' => 2000,
                'is_active' => true,
                'default_prompt' => 'Bạn là người cố vấn học tập. Hãy thiết lập lộ trình học tập cá nhân hóa dựa trên kết quả thi của học sinh.',
            ],
            [
                'provider' => 'openrouter',
                'model_name' => $freeModel,
                'purpose' => AiConfig::PURPOSE_GENERAL,
                'temperature' => 0.8,
                'max_tokens' => 1000,
                'is_active' => true,
                'default_prompt' => 'Bạn là trợ lý học tập Quiz Lumina. Hãy hỗ trợ học sinh giải đáp các thắc mắc về kiến thức một cách ngắn gọn, súc tích.',
            ],
        ];

        foreach ($configs as $config) {
            AiConfig::updateOrCreate(
                ['purpose' => $config['purpose'], 'is_active' => true],
                array_merge($config, [
                    'api_key' => $encryptedKey,
                    'created_by' => $admin ? $admin->id : null
                ])
            );
        }

        $this->command->info('Đã khởi tạo/cập nhật 5 cấu hình AI (OpenRouter Free) thành công!');
    }
}
