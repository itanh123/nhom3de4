<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\ChatController;

$controller = app(ChatController::class);
$request = Request::create('/chat', 'POST', ['message' => 'Hello']);
$response = $controller->chat($request);

echo $response->getContent();
