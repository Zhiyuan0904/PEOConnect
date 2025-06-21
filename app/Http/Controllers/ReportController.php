<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Export the full report, optionally filtered by year.
     */
    public function exportFullReport(Request $request)
    {
        try {
            $year = $request->input('year'); // Optional year filter

            // Get surveys, optionally filtered by year
            $surveyQuery = Survey::query();

            if ($year) {
                $surveyQuery->whereYear('created_at', $year);
            }

            $surveys = $surveyQuery->get();

            if ($surveys->isEmpty()) {
                return response()->json([
                    'error' => true,
                    'message' => 'No surveys found for the selected year.',
                ], 404);
            }

            $reportData = [];

            foreach ($surveys as $survey) {
                $totalStudents = User::where('role', 'student')->count(); // You can customize this if surveys are targeted to specific students only

                $responded = SurveyResponse::where('survey_id', $survey->id)
                                ->distinct('user_id')
                                ->count('user_id');

                $percentage = $totalStudents > 0 ? round(($responded / $totalStudents) * 100) : 0;

                $reportData[] = [
                    'survey_title' => $survey->title,
                    'responded' => $responded,
                    'total' => $totalStudents,
                    'percentage' => $percentage,
                    'created_at' => $survey->created_at->format('Y'),
                ];
            }

            // Load PDF view and pass data
            $pdf = Pdf::loadView('reports.progress', [
                'reportData' => $reportData,
                'year' => $year
            ]);

            $filename = $year ? "survey_report_$year.pdf" : "survey_report.pdf";

            return $pdf->download($filename);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to generate PDF',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function getYearlyComparison()
    {
        $years = Survey::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year');

        $result = [];

        foreach ($years as $year) {
            $surveys = Survey::whereYear('created_at', $year)->get();
            $totalSurveys = $surveys->count();
            $totalPercentage = 0;

            foreach ($surveys as $survey) {
                $totalStudents = User::where('role', 'student')->count(); // Adjust if survey targets are more specific
                $responded = SurveyResponse::where('survey_id', $survey->id)
                    ->distinct('user_id')
                    ->count('user_id');
                $completion = $totalStudents > 0 ? ($responded / $totalStudents) * 100 : 0;
                $totalPercentage += $completion;
            }

            $avgCompletion = $totalSurveys > 0 ? round($totalPercentage / $totalSurveys, 1) : 0;

            $result[] = [
                'year' => $year,
                'avg_completion' => $avgCompletion,
            ];
        }

        return response()->json($result);
    }

    public function getAvailableYears()
    {
        $years = Survey::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return response()->json($years);
    }
}
