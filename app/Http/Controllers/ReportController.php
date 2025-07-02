<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SurveyDistribution;
use App\Models\CurriculumContent;
use App\Models\PEO;

class ReportController extends Controller
{
    public function exportFullReport(Request $request)
    {
        try {
            $year = $request->input('year');

            // Fix: ensure valid numeric year
            if (!is_numeric($year)) {
                $year = null;
            }

            if ($year) {
            // Get survey IDs that have responses in the selected year
            $surveyIds = SurveyResponse::whereYear('created_at', $year)
                ->pluck('survey_id')
                ->unique();

            $surveys = Survey::whereIn('id', $surveyIds)->get();
            } else {
                $surveys = Survey::all();
            }


            if ($surveys->isEmpty()) {
                return response()->json([
                    'error' => true,
                    'message' => 'No surveys found for the selected year.',
                ], 404);
            }

            $reportData = [];

            foreach ($surveys as $survey) {
                $targetRoles = SurveyDistribution::where('survey_id', $survey->id)
                    ->pluck('target_role')
                    ->unique()
                    ->toArray();

                $totalUsers = User::whereIn('role', $targetRoles)->count();

                $responded = SurveyResponse::where('survey_id', $survey->id)
                    ->distinct('user_id')
                    ->count('user_id');

                $reportData[] = [
                    'survey_title' => $survey->title,
                    'target_role' => implode(', ', $targetRoles),
                    'responded' => $responded,
                    'total' => $totalUsers,
                    'percentage' => $totalUsers > 0 ? round(($responded / $totalUsers) * 100) : 0,
                    'created_at' => $survey->created_at->format('Y'),
                ];
            }

            // Top PEOs
            $peoCounts = [];
            $allPEOIds = CurriculumContent::pluck('peo_ids')->flatten();

            foreach ($allPEOIds as $ids) {
                if (is_array($ids)) {
                    foreach ($ids as $id) {
                        $peoCounts[$id] = ($peoCounts[$id] ?? 0) + 1;
                    }
                } elseif (is_numeric($ids)) {
                    $peoCounts[$ids] = ($peoCounts[$ids] ?? 0) + 1;
                }
            }

            arsort($peoCounts);
            $peoDetails = PEO::whereIn('id', array_keys($peoCounts))->get();

            $topPeos = [];
            foreach ($peoCounts as $id => $count) {
                $peo = $peoDetails->firstWhere('id', $id);
                if ($peo) {
                    $topPeos[] = [
                        'code' => $peo->code,
                        'description' => $peo->description,
                        'count' => $count
                    ];
                }
            }

            // Summary
            $summary = [
                'total_surveys' => count($reportData),
                'total_users' => collect($reportData)->sum('total'),
                'total_responded' => collect($reportData)->sum('responded'),
                'avg_completion' => collect($reportData)->avg('percentage') ?? 0,
            ];

            $pdf = Pdf::loadView('reports.progress', [
                'reportData' => $reportData,
                'topPeos' => $topPeos,
                'summary' => $summary,
                'year' => $year,
                'generated_at' => now()->format('F j, Y, g:i A')
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
        // Get unique years based on response submission date
        $years = SurveyResponse::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->pluck('year');

        $result = [];

        foreach ($years as $year) {
            // Get survey IDs with responses in that year
            $surveyIds = SurveyResponse::whereYear('created_at', $year)
                ->pluck('survey_id')
                ->unique();

            $surveys = Survey::whereIn('id', $surveyIds)->get();
            $totalSurveys = $surveys->count();
            $totalPercentage = 0;

            foreach ($surveys as $survey) {
                $targetRoles = SurveyDistribution::where('survey_id', $survey->id)
                    ->pluck('target_role')
                    ->unique()
                    ->toArray();

                $totalUsers = User::whereIn('role', $targetRoles)->count();
                $responded = SurveyResponse::where('survey_id', $survey->id)
                    ->whereYear('created_at', $year)
                    ->distinct('user_id')
                    ->count('user_id');

                $completion = $totalUsers > 0 ? ($responded / $totalUsers) * 100 : 0;
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
