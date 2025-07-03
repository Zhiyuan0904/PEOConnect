<?php

namespace App\Http\Controllers;

use App\Models\SurveyDistribution;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Services\SurveyDistributionMailer;
use Carbon\Carbon;

class SurveyDistributionController extends Controller
{
    // Admin View: List all distributions
    public function index()
    {
        $distributions = SurveyDistribution::with('survey')->orderBy('scheduled_active_date', 'desc')->get();
        return response()->json($distributions);
    }

    // Admin Action: Create a new distribution and send email
    public function store(Request $request)
    {
        $validated = $request->validate([
            'survey_id' => 'required|exists:surveys,id',
            'target_role' => 'required|in:student,alumni',
            'date_field' => 'required|in:enroll_date,expected_graduate_date',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'scheduled_active_date' => 'required|date',
            'scheduled_end_date' => 'required|date|after_or_equal:scheduled_active_date',
        ]);

        // Set is_active based on whether the scheduled_active_date is in the past
        $validated['is_active'] = Carbon::parse($validated['scheduled_active_date'])->isPast() ? 1 : 0;

        // Create the distribution
        $distribution = SurveyDistribution::create($validated);

        // Generate the survey link (Vue is served from Laravel)
        $surveyLink = url('/respond/surveys/' . $validated['survey_id']);

        // Get all users with matching role
        $recipients = User::where('role', $validated['target_role'])
            ->whereBetween($validated['date_field'], [$validated['start_date'], $validated['end_date']])
            ->pluck('email');


        // Send email to each user
        foreach ($recipients as $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                (new SurveyDistributionMailer($user->email, $user->name, [
                    $distribution->survey->title => $surveyLink
                ]))->send();
            }
        }


        return response()->json([
            'message' => 'Survey distribution created and emails sent!',
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
            'scheduled_end_date' => 'date|after_or_equal:scheduled_active_date',
            'is_active' => 'boolean',
        ]);


        $distribution->update($validated);

        return response()->json([
            'message' => 'Survey distribution updated successfully!',
            'distribution' => $distribution
        ]);
    }
}
