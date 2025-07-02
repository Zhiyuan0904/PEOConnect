<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CurriculumContent;
use App\Models\PEO;
use App\Models\SurveyDistribution;

class ProgressController extends Controller
{
    // Summary progress for all survey titles
    public function index()
    {
        $surveyTitles = Survey::pluck('title')->unique();
        $progressData = [];

        foreach ($surveyTitles as $title) {
            $surveyIds = Survey::where('title', $title)->pluck('id');

            // Get the unique target roles from SurveyDistribution
            $targetRoles = SurveyDistribution::whereIn('survey_id', $surveyIds)
                ->pluck('target_role')
                ->unique()
                ->toArray();

            // Total users based on target roles
            $totalUsers = User::whereIn('role', $targetRoles)->count();

            // Responders
            $responded = SurveyResponse::whereIn('survey_id', $surveyIds)
                ->distinct('user_id')
                ->count('user_id');

            $percentage = $totalUsers > 0 ? round(($responded / $totalUsers) * 100) : 0;

            $progressData[] = [
                'survey_title' => $title,
                'responded' => $responded,
                'total' => $totalUsers,
                'percentage' => $percentage,
            ];
        }

        return response()->json($progressData);
    }

    // Detail progress for a specific survey title
    public function detail($title)
    {
        $surveyIds = Survey::where('title', $title)->pluck('id');

        // Get all related survey distributions
        $distributions = SurveyDistribution::whereIn('survey_id', $surveyIds)->get();

        $allUsers = collect();

        foreach ($distributions as $dist) {
            $query = User::where('role', $dist->target_role);

            if ($dist->start_date) {
                $query->whereDate('enroll_date', '>=', $dist->start_date);
            }

            if ($dist->end_date) {
                $query->whereDate('enroll_date', '<=', $dist->end_date);
            }

            $users = $query->get();
            $allUsers = $allUsers->merge($users);
        }

        // Remove duplicates
        $allUsers = $allUsers->unique('id');

        // Get actual responders
        $responders = SurveyResponse::whereIn('survey_id', $surveyIds)
            ->distinct('user_id')
            ->pluck('user_id');

        $result = $allUsers->map(function ($user) use ($responders) {
            return [
                'name' => $user->name,
                'email' => $user->email,
                'responded' => $responders->contains($user->id),
            ];
        });

        return response()->json([
            'survey_title' => $title,
            'students' => $result->values(),
        ]);
    }

    public function topPEOs(Request $request)
    {
        $allPEOIds = CurriculumContent::pluck('peo_ids')->flatten();

        $counts = [];
        foreach ($allPEOIds as $ids) {
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    $counts[$id] = ($counts[$id] ?? 0) + 1;
                }
            } elseif (is_numeric($ids)) {
                $counts[$ids] = ($counts[$ids] ?? 0) + 1;
            }
        }

        arsort($counts);

        $peoDetails = PEO::whereIn('id', array_keys($counts))->get();

        $result = [];
        foreach ($counts as $id => $count) {
            $peo = $peoDetails->firstWhere('id', $id);
            if ($peo) {
                $result[] = [
                    'code' => $peo->code,
                    'description' => $peo->description,
                    'count' => $count
                ];
            }
        }

        return response()->json($result);
    }
}
