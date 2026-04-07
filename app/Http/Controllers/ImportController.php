<?php

namespace App\Http\Controllers;

use App\Models\ImportHistory;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Topic;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = ImportHistory::with(['user', 'topic']);

        if ($user->role === 'teacher') {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $imports = $query->orderByDesc('created_at')->paginate(15);

        return view('imports.index', compact('imports'));
    }

    public function create()
    {
        $topics = Topic::orderBy('name')->get();
        return view('imports.create', compact('topics'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'file' => 'required|file|mimes:csv,txt,xlsx|max:10240',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $path = $file->store('imports', 'public');

        $import = ImportHistory::create([
            'user_id' => auth()->id(),
            'topic_id' => $request->topic_id,
            'file_name' => $originalName,
            'file_path' => $path,
            'total_rows' => 0,
            'success_rows' => 0,
            'failed_rows' => 0,
            'status' => 'processing',
            'created_at' => now(),
        ]);

        try {
            $extension = strtolower($file->getClientOriginalExtension());
            
            if ($extension === 'csv' || $extension === 'txt') {
                $result = $this->processCsv($file, $request->topic_id);
            } else {
                $result = $this->processExcel($file, $request->topic_id);
            }

            $import->update([
                'total_rows' => $result['total'],
                'success_rows' => $result['success'],
                'failed_rows' => $result['failed'],
                'status' => $result['failed'] > 0 ? 'completed_with_errors' : 'completed',
                'error_message' => $result['errors'] ?? null,
            ]);

            ActivityLogger::importQuestions($import);

            $msg = "Nhập thành công {$result['success']}/{$result['total']} câu hỏi.";
            if ($result['failed'] > 0) {
                $msg .= " Có {$result['failed']} dòng lỗi.";
            }

            return redirect()->route('imports.show', $import)->with('success', $msg);

        } catch (\Exception $e) {
            $import->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Lỗi khi xử lý file: ' . $e->getMessage())->withInput();
        }
    }

    public function show(ImportHistory $import)
    {
        $user = auth()->user();
        if ($user->role === 'teacher' && $import->user_id !== $user->id) {
            abort(403);
        }

        $import->load(['user', 'topic']);

        return view('imports.show', compact('import'));
    }

    protected function processCsv($file, int $topicId): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        $headers = fgetcsv($handle);
        $headers = array_map('strtolower', array_map('trim', $headers));

        $total = 0;
        $success = 0;
        $failed = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            $total++;
            $data = array_combine($headers, $row);

            try {
                DB::transaction(function () use ($data, $topicId, &$success) {
                    $type = $data['type'] ?? 'single_choice';
                    $type = in_array($type, ['single_choice', 'multiple_choice', 'fill_in_blank']) ? $type : 'single_choice';

                    $question = Question::create([
                        'topic_id' => $topicId,
                        'content' => trim($data['content'] ?? ''),
                        'type' => $type,
                        'difficulty' => $data['difficulty'] ?? 'medium',
                        'explanation' => $data['explanation'] ?? null,
                        'is_active' => true,
                        'created_by' => auth()->id(),
                    ]);

                    for ($i = 1; $i <= 4; $i++) {
                        $answerContent = trim($data["answer_{$i}"] ?? '');
                        if (empty($answerContent)) continue;

                        $isCorrect = strtolower(trim($data["answer_{$i}_correct"] ?? '')) === 'true';

                        if ($type === 'fill_in_blank') {
                            $isCorrect = true;
                        }

                        Answer::create([
                            'question_id' => $question->id,
                            'option_text' => $answerContent,
                            'is_correct' => $isCorrect,
                            'display_order' => $i,
                        ]);
                    }

                    $success++;
                });
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Dòng {$total}: " . $e->getMessage();
            }
        }

        fclose($handle);

        return [
            'total' => $total,
            'success' => $success,
            'failed' => $failed,
            'errors' => !empty($errors) ? implode("\n", $errors) : null,
        ];
    }

    protected function processExcel($file, int $topicId): array
    {
        return $this->processCsv($file, $topicId);
    }

    public function template()
    {
        $headers = ['content', 'type', 'difficulty', 'explanation', 'answer_1', 'answer_1_correct', 'answer_2', 'answer_2_correct', 'answer_3', 'answer_3_correct', 'answer_4', 'answer_4_correct'];

        $sample = [
            ['Đâu là thủ đô của Việt Nam?', 'single_choice', 'easy', 'Hà Nội là thủ đô của Việt Nam từ năm 1010.', 'Hà Nội', 'true', 'TP Hồ Chí Minh', 'false', 'Đà Nẵng', 'false', 'Hải Phòng', 'false'],
            ['Chọn các ngôn ngữ lập trình phổ biến:', 'multiple_choice', 'medium', 'Các ngôn ngữ này được sử dụng rộng rãi.', 'Python', 'true', 'HTML', 'true', 'Java', 'true', 'Pascal', 'false'],
        ];

        $content = implode("\n", array_map(function($row) {
            return implode(',', $row);
        }, array_merge([$headers], $sample)));

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="import_template.csv"');
    }
}
