<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;
use App\Models\SurveyDistribution;
use Illuminate\Support\Facades\Validator;

class SurveyController extends Controller
{
    // ✅ List all surveys
    public function index()
    {
        return Survey::all();
    }

    // ✅ Store new survey (normal create)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array|min:1',
            'questions.*.questionType' => 'required|string|in:short-answer,multiple-choice',
            'questions.*.questionText' => 'required|string|max:1000',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*' => 'nullable|string|max:255',
        ]);

        $survey = Survey::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'questions' => $validated['questions'], // Stored as JSON
        ]);

        return response()->json($survey, 201);
    }

    // ✅ Import survey from CSV file (automation feature)
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
        $title = $header[0] ?? 'Untitled Survey';
        $description = $header[1] ?? '';

        $questions = [];
        foreach ($rows as $row) {
            $columns = array_map('trim', $row);
            if (empty($columns[2])) continue; // Skip empty question

            $questionText = $columns[2];
            $questionType = $columns[3] ?? 'short-answer';
            $options = array_slice($columns, 4);
            $cleanOptions = array_filter($options, fn($opt) => $opt !== '');

            $questions[] = [
                'questionType' => $questionType,
                'questionText' => $questionText,
                'options' => $questionType === 'multiple-choice' ? array_values($cleanOptions) : [],
            ];
        }

        // ✅ Re-validate parsed questions
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
            'description' => $description,
            'questions' => $questions,
        ]);

        return response()->json([
            'message' => 'Survey imported successfully!',
            'survey' => $survey,
        ], 201);
    }

    // ✅ Show single survey
    public function show($id)
    {
        return Survey::findOrFail($id);
    }

    // ✅ Update survey
    public function update(Request $request, $id)
    {
        $survey = Survey::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array|min:1',
            'questions.*.questionType' => 'required|string|in:short-answer,multiple-choice',
            'questions.*.questionText' => 'required|string|max:1000',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*' => 'nullable|string|max:255',
        ]);

        $survey->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'questions' => $validated['questions'],
        ]);

        return response()->json($survey);
    }

    // ✅ Delete survey
    public function destroy($id)
    {
        $survey = Survey::findOrFail($id);
        $survey->delete();

        return response()->json(null, 204);
    }

    // ✅ Get available surveys (for student use)
    public function availableSurveys()
    {
        $today = now()->toDateString();

        $surveys = SurveyDistribution::with('survey')
            ->where('is_active', true)
            ->whereDate('scheduled_active_date', '<=', $today)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get()
            ->filter(fn($distribution) => $distribution->survey !== null)
            ->map(fn($distribution) => [
                'id' => $distribution->survey->id,
                'title' => $distribution->survey->title,
                'description' => $distribution->survey->description,
                'questions' => $distribution->survey->questions,
            ])
            ->values();

        return response()->json($surveys);
    }
}
