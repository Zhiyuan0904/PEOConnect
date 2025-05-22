<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;
use App\Models\SurveyDistribution;

class SurveyController extends Controller
{
    // List all surveys
    public function index()
    {
        return Survey::all();
    }

    // Store new survey
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
            'questions' => $validated['questions'], // Save as JSON
        ]);

        return response()->json($survey, 201);
    }

    // Show single survey
    public function show($id)
    {
        return Survey::findOrFail($id);
    }

    // Update survey
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

    // Delete survey
    public function destroy($id)
    {
        $survey = Survey::findOrFail($id);
        $survey->delete();

        return response()->json(null, 204);
    }

    public function availableSurveys()
    {
        $today = now()->toDateString();

        $surveys = SurveyDistribution::with('survey')
            ->where('is_active', true)
            ->whereDate('scheduled_active_date', '<=', $today)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get()
            ->filter(function ($distribution) {
                return $distribution->survey !== null; // Ensure survey exists
            })
            ->map(function ($distribution) {
                return [
                    'id' => $distribution->survey->id,
                    'title' => $distribution->survey->title,
                    'description' => $distribution->survey->description,
                    'questions' => $distribution->survey->questions,
                ];
            })
            ->values(); // Reindex array

        return response()->json($surveys);
    }
}
