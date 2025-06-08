<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Barryvdh\DomPDF\Facade\Pdf;  // Make sure you installed barryvdh/laravel-dompdf

class ReportController extends Controller
{
    public function exportFullReport()
    {
        try {
            // Prepare data
            $totalStudents = User::where('role', 'student')->count();
            $surveyTitles = Survey::pluck('title')->unique();

            $reportData = [];

            foreach ($surveyTitles as $title) {
                $surveyIds = Survey::where('title', $title)->pluck('id');
                $responded = SurveyResponse::whereIn('survey_id', $surveyIds)
                                ->distinct('user_id')
                                ->count('user_id');
                $percentage = $totalStudents > 0 ? round(($responded / $totalStudents) * 100) : 0;

                $reportData[] = [
                    'survey_title' => $title,
                    'responded' => $responded,
                    'total' => $totalStudents,
                    'percentage' => $percentage,
                ];
            }

            $pdf = Pdf::loadView('reports.progress', ['reportData' => $reportData]);
            return $pdf->download('survey_report.pdf');

        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to generate PDF',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
