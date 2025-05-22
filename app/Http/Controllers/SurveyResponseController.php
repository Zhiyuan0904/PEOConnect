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
            'responses' => json_encode($validated['responses']), // <-- THIS is the main fix
        ]);

        return response()->json([
            'message' => 'Survey submitted successfully',
            'data' => $response,
        ], 201);
    }

    // Admin fetches all responses
    public function index()
    {
        $responses = SurveyResponse::with('survey', 'user')->latest()->get();

        return response()->json($responses);
    }

}
