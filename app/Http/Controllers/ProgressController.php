<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    // Summary progress for all survey titles
    public function index()
    {
        $totalStudents = User::where('role', 'student')->count();
        $surveyTitles = Survey::pluck('title')->unique();

        $progressData = [];

        foreach ($surveyTitles as $title) {
            $surveyIds = Survey::where('title', $title)->pluck('id');

            $responded = SurveyResponse::whereIn('survey_id', $surveyIds)
                ->distinct('user_id')
                ->count('user_id');

            $percentage = $totalStudents > 0 ? round(($responded / $totalStudents) * 100) : 0;

            $progressData[] = [
                'survey_title' => $title,
                'responded' => $responded,
                'total' => $totalStudents,
                'percentage' => $percentage,
            ];
        }

        return response()->json($progressData);
    }

    // Detail progress for a specific survey title
    public function detail($title)
    {
        $surveyIds = Survey::where('title', $title)->pluck('id');

        // Students who have responded
        $responders = SurveyResponse::whereIn('survey_id', $surveyIds)
            ->distinct('user_id')
            ->pluck('user_id');

        $students = User::where('role', 'student')->get()->map(function ($student) use ($responders) {
            return [
                'name' => $student->name,
                'email' => $student->email,
                'responded' => $responders->contains($student->id),
            ];
        });

        return response()->json([
            'survey_title' => $title,
            'students' => $students,
        ]);
    }
}
