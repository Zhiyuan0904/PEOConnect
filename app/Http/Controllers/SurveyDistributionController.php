<?php

namespace App\Http\Controllers;

use App\Models\SurveyDistribution;
use App\Models\Survey;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SurveyDistributionController extends Controller
{
    // Admin View: List all distributions
    public function index()
    {
        $distributions = SurveyDistribution::with('survey')->orderBy('scheduled_active_date', 'desc')->get();
        return response()->json($distributions);
    }

    // Admin Action: Create a new distribution
    public function store(Request $request)
    {
        $validated = $request->validate([
            'survey_id' => 'required|exists:surveys,id',
            'target_role' => 'required|in:student,alumni',
            'date_field' => 'required|in:enroll_date,expected_graduate_date',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'scheduled_active_date' => 'required|date',
        ]);

        // ✨ Auto set is_active based on today's date
        $validated['is_active'] = Carbon::parse($validated['scheduled_active_date'])->isPast() ? 1 : 0;

        $distribution = SurveyDistribution::create($validated);

        return response()->json([
            'message' => 'Survey distribution created successfully!',
            'distribution' => $distribution
        ], 201);
    }

    // (Optional) Admin Action: Update existing distribution
    public function update(Request $request, $id)
    {
        $distribution = SurveyDistribution::findOrFail($id);

        $validated = $request->validate([
            'start_date' => 'date',
            'end_date' => 'date',
            'scheduled_active_date' => 'date',
            'is_active' => 'boolean',
        ]);

        $distribution->update($validated);

        return response()->json([
            'message' => 'Survey distribution updated successfully!',
            'distribution' => $distribution
        ]);
    }
}
