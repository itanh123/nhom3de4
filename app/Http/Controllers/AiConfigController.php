<?php

namespace App\Http\Controllers;

use App\Models\AiConfig;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class AiConfigController extends Controller
{
    public function index(Request $request)
    {
        $query = AiConfig::with(['creator', 'updater']);

        if ($request->filled('provider')) {
            $query->where('provider', $request->provider);
        }

        if ($request->filled('purpose')) {
            $query->where('purpose', $request->purpose);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $configs = $query->orderByDesc('is_active')->orderByDesc('created_at')->paginate(15);

        return view('admin.ai_configs.index', compact('configs'));
    }

    public function create()
    {
        return view('admin.ai_configs.create');
    }

    public function store(Request $request)
    {
        $allowedProviders = ['openrouter', 'groq', 'openai', 'anthropic', 'google'];
        
        $request->validate([
            'provider' => 'required|string|in:' . implode(',', $allowedProviders),
            'model_name' => 'required|string|max:100',
            'purpose' => 'required|in:question_generation,answer_explanation,result_evaluation,learning_path,general',
            'api_key' => 'required|string|max:255',
            'base_url' => 'nullable|string|max:255',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'max_tokens' => 'nullable|integer|min:100|max:100000',
            'default_prompt' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = $request->boolean('is_active', false);

        if ($isActive) {
            AiConfig::where('purpose', $request->purpose)->update(['is_active' => false]);
        }

        $config = AiConfig::create([
            'provider' => $request->provider,
            'model_name' => $request->model_name,
            'purpose' => $request->purpose,
            'api_key' => encrypt($request->api_key),
            'base_url' => $request->base_url,
            'temperature' => $request->temperature ?? 0.7,
            'max_tokens' => $request->max_tokens ?? 2000,
            'default_prompt' => $request->default_prompt,
            'is_active' => $isActive,
            'created_by' => auth()->id(),
        ]);

        ActivityLogger::createAiConfig($config);

        return redirect()->route('admin.ai-configs.index')->with('success', 'Tạo cấu hình AI thành công!');
    }

    public function show(AiConfig $aiConfig)
    {
        return view('admin.ai_configs.show', compact('aiConfig'));
    }

    public function edit(AiConfig $aiConfig)
    {
        return view('admin.ai_configs.edit', compact('aiConfig'));
    }

    public function update(Request $request, AiConfig $aiConfig)
    {
        $allowedProviders = ['openrouter', 'groq', 'openai', 'anthropic', 'google'];
        
        $request->validate([
            'provider' => 'required|string|in:' . implode(',', $allowedProviders),
            'model_name' => 'required|string|max:100',
            'purpose' => 'required|in:question_generation,answer_explanation,result_evaluation,learning_path,general',
            'api_key' => 'nullable|string|max:255',
            'base_url' => 'nullable|string|max:255',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'max_tokens' => 'nullable|integer|min:100|max:100000',
            'default_prompt' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = $request->boolean('is_active');

        if ($isActive) {
            AiConfig::where('purpose', $request->purpose)->where('id', '!=', $aiConfig->id)->update(['is_active' => false]);
        }

        $data = $request->only(['provider', 'model_name', 'purpose', 'base_url', 'temperature', 'max_tokens', 'default_prompt']);

        if ($request->filled('api_key')) {
            $data['api_key'] = encrypt($request->api_key);
        }

        $data['is_active'] = $isActive;
        $data['updated_by'] = auth()->id();
        $aiConfig->update($data);

        ActivityLogger::updateAiConfig($aiConfig);

        return redirect()->route('admin.ai-configs.index')->with('success', 'Cập nhật cấu hình AI thành công!');
    }

    public function destroy(AiConfig $aiConfig)
    {
        ActivityLogger::deleteAiConfig($aiConfig);
        $aiConfig->delete();

        return redirect()->route('admin.ai-configs.index')->with('success', 'Xóa cấu hình AI thành công!');
    }

    public function toggle(AiConfig $aiConfig)
    {
        $newStatus = !$aiConfig->is_active;

        if ($newStatus) {
            AiConfig::where('purpose', $aiConfig->purpose)->where('id', '!=', $aiConfig->id)->update(['is_active' => false]);
        }

        $aiConfig->update(['is_active' => $newStatus]);
        ActivityLogger::toggleAiConfig($aiConfig);

        return back()->with('success', $newStatus ? 'Đã bật cấu hình AI!' : 'Đã tắt cấu hình AI!');
    }
}
