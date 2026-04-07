<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$configs = \App\Models\AiConfig::all();
echo "--- Các cấu hình AI trong CSDL ---\n";
foreach($configs as $cfg) {
    echo "- ID: {$cfg->id} | {$cfg->provider}/{$cfg->model_name} | Purpose: {$cfg->purpose} | Active: {$cfg->is_active}\n";
}

$aiService = app(\App\Services\AiService::class);

echo "\n--- Thử gọi AI (Tạo 1 câu hỏi test) ---\n";
// Nếu không cấu hình cụ thể purpose thì nó sẽ báo lỗi, ta có thể tạm override db:
$activeConfig = \App\Models\AiConfig::active()->first();
if (!$activeConfig) {
    echo "KHÔNG TÌM THẤY CẤU HÌNH AI ACTIVE NÀO TRONG DB.\n";
    exit;
}

// Ensure the first active config supports question generation for testing
$oldPurpose = $activeConfig->purpose;
$activeConfig->purpose = 'question_generation';
$activeConfig->save();

$result = $aiService->generateQuestions([
    'topic' => 'Toán cơ bản',
    'number' => 1,
    'type' => 'single_choice',
    'difficulty' => 'easy'
]);

// Restore old purpose
$activeConfig->purpose = $oldPurpose;
$activeConfig->save();

echo "Kết quả API:\n";
print_r($result);
