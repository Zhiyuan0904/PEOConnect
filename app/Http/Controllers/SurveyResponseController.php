<?php

namespace App\Http\Controllers;

use App\Models\SurveyResponse;
use Illuminate\Http\Request;

class SurveyResponseController extends Controller
{
    // Student submits survey response
    public function store(Request $request)
    {
        $validated = $request->validate([
            'survey_id' => 'required|exists:surveys,id',
            'responses' => 'required|array',
        ]);

        $response = SurveyResponse::create([
            'survey_id' => $validated['survey_id'],
            'user_id' => $request->user()->id,
            'responses' => json_encode($validated['responses']),
        ]);

        return response()->json([
            'message' => 'Survey submitted successfully',
            'data' => $response,
        ], 201);
    }

    // Admin fetches responses with pagination + optional search
    public function index(Request $request)
    {
        $query = SurveyResponse::with('survey', 'user')->latest();

        if ($request->filled('search')) {
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('survey', function ($surveyQuery) use ($searchTerm) {
                    $surveyQuery->where('title', 'like', '%' . $searchTerm . '%');
                })->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                    $userQuery->where('name', 'like', '%' . $searchTerm . '%');
                });
            });
        }

        $perPage = $request->input('per_page', 10);
        $paginator = $query->paginate($perPage);

        $data = collect($paginator->items())->map(function ($item) {
            return [
                'id' => $item->id,
                'survey_title' => optional($item->survey)->title,
                'user_name' => optional($item->user)->name,
                'answers' => collect(json_decode($item->responses, true))
                    ->map(function ($response, $index) use ($item) {
                        $questionText = $item->survey->questions[$index]['questionText'] ?? "Question ".($index+1);
                        return [
                            'question' => $questionText,
                            'response' => $response
                        ];
                    })->values()->toArray(),
                'created_at' => $item->created_at,
            ];
        });

        return response()->json([
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'data' => $data->values()->toArray(),
        ]);
    }

    // Admin deletes specific response
    public function destroy($id)
    {
        $response = SurveyResponse::findOrFail($id);
        $response->delete();

        return response()->json(['message' => 'Response deleted successfully.']);
    }
}
