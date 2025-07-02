<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyDistribution;
use App\Models\SurveyResponse; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class SurveyController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $allowedRoles = ['admin', 'quality team', 'dean', 'lecturer'];
        if (in_array($user->role, $allowedRoles)) {
            return Survey::all();
        }
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    public function store(Request $request)
    {
        $titleLower = strtolower($request->title);

        $validator = Validator::make($request->all(), [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('surveys')->where(fn ($q) =>
                    $q->whereRaw('LOWER(title) = ?', [$titleLower])
                ),
            ],
            'description' => 'nullable|string',
            'questions' => 'required|array|min:1',
            'questions.*.questionType' => 'required|in:short-answer,multiple-choice',
            'questions.*.questionText' => 'required|string|max:1000',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $survey = Survey::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'questions' => $data['questions'],
        ]);

        return response()->json($survey, 201);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $rows = array_map('str_getcsv', file($path));

        if (empty($rows) || count($rows[0]) < 4) {
            return response()->json(['error' => 'Invalid CSV format.'], 422);
        }

        $header = array_shift($rows);
        $title = $header[0] ?: 'Untitled Survey';
        $description = $header[1] ?? '';

        $questions = [];
        foreach ($rows as $row) {
            $col = array_map('trim', $row);
            if (empty($col[2])) continue;
            $questions[] = [
                'questionType' => $col[3] ?? 'short-answer',
                'questionText' => $col[2],
                'options' => $col[3] === 'multiple-choice'
                    ? array_values(array_filter(array_slice($col, 4), fn($opt) => $opt !== ''))
                    : [],
            ];
        }

        $validator = Validator::make(['questions' => $questions], [
            'questions' => 'required|array|min:1',
            'questions.*.questionType' => 'required|in:short-answer,multiple-choice',
            'questions.*.questionText' => 'required|string|max:1000',
            'questions.*.options.*' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid question format.',
                'details' => $validator->errors()
            ], 422);
        }

        $survey = Survey::create([
            'title' => $title,
            'description' => $description ?: null,
            'questions' => $questions,
        ]);

        return response()->json([
            'message' => 'Survey imported successfully!',
            'survey' => $survey
        ], 201);
    }

    public function show($id)
    {
        return Survey::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $survey = Survey::findOrFail($id);
        $titleLower = strtolower($request->title);

        $validator = Validator::make($request->all(), [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('surveys')->ignore($survey->id)
                    ->where(fn ($q) =>
                        $q->whereRaw('LOWER(title) = ?', [$titleLower])
                    ),
            ],
            'description' => 'nullable|string',
            'questions' => 'required|array|min:1',
            'questions.*.questionType' => 'required|in:short-answer,multiple-choice',
            'questions.*.questionText' => 'required|string|max:1000',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $survey->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'questions' => $data['questions'],
        ]);

        return response()->json($survey);
    }

    public function destroy($id)
    {
        Survey::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    public function availableSurveys(Request $request)
    {
        $user = $request->user();
        $today = now();

        if (!in_array($user->role, ['student', 'alumni'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $distributions = SurveyDistribution::with('survey')
            ->where('is_active', true)
            ->whereDate('scheduled_active_date', '<=', $today)
            ->whereDate('scheduled_end_date', '>=', $today)
            ->where('target_role', $user->role)
            ->get();

        $surveys = $distributions->filter(function ($dist) use ($user) {
            if (!$dist->survey) return false;

            $field = $dist->date_field;
            $userDate = $user->{$field};

            if (!$userDate) return false;

            return $userDate >= $dist->start_date && $userDate <= $dist->end_date;
        })->filter(function ($dist) use ($user) {
            return !SurveyResponse::where('survey_id', $dist->survey->id)
                ->where('user_id', $user->id)
                ->exists();
        })->map(function ($dist) {
            return [
                'id' => $dist->survey->id,
                'title' => $dist->survey->title,
                'description' => $dist->survey->description,
                'questions' => $dist->survey->questions,
                'target_role' => $dist->target_role,
            ];
        })->values();

        return response()->json($surveys);
    }



    public function checkTitle(Request $request)
    {
        $title = $request->query('title');
        $exists = Survey::whereRaw('LOWER(title) = ?', [strtolower($title)])->exists();
        return response()->json(['exists' => $exists]);
    }
}
