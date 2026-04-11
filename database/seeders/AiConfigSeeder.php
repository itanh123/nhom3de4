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

        $configs = [
            [
                'provider' => 'openai',
                'model_name' => 'gpt-4.1-mini',
                'purpose' => AiConfig::PURPOSE_QUESTION_GENERATION,
                'temperature' => 0.7,
                'max_tokens' => 2000,
                'is_active' => true,
                'default_prompt' => 'Bạn là một giáo viên chuyên nghiệp. Hãy tạo câu hỏi trắc nghiệm dựa trên nội dung được cung cấp.',
            ],
            [
                'provider' => 'openai',
                'model_name' => 'gpt-4.1-mini',
                'purpose' => AiConfig::PURPOSE_ANSWER_EXPLANATION,
                'temperature' => 0.5,
                'max_tokens' => 1000,
                'is_active' => true,
                'default_prompt' => 'Bạn là một giáo viên. Hãy giải thích tại sao đáp án này đúng hoặc sai.',
            ],
            [
                'provider' => 'openai',
                'model_name' => 'gpt-4.1-mini',
                'purpose' => AiConfig::PURPOSE_RESULT_EVALUATION,
                'temperature' => 0.6,
                'max_tokens' => 1500,
                'is_active' => true,
                'default_prompt' => 'Bạn là chuyên gia giáo dục. Hãy đánh giá kết quả bài thi và đưa ra đề xuất cải thiện.',
            ],
        ];

        foreach ($configs as $config) {
            AiConfig::firstOrCreate(
                [
                    'provider' => $config['provider'],
                    'model_name' => $config['model_name'],
                    'purpose' => $config['purpose'],
                ],
                array_merge($config, ['created_by' => $admin->id])
            );
        }
    }
}
