<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CurriculumContent;
use Illuminate\Support\Facades\Auth;


class CurriculumContentController extends Controller
{
    public function index()
    {
        try {
            // Test the DB query
            $data = \App\Models\CurriculumContent::all();
            return response()->json($data);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error occurred',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'peo_ids' => 'required|array',
        ]);

        return CurriculumContent::create([
            'title' => $request->title,
            'description' => $request->description,
            'peo_ids' => $request->peo_ids,
            'created_by' => Auth::id(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $content = CurriculumContent::findOrFail($id);

        $this->authorizeAccess($content); // optional role check

        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'peo_ids' => 'required|array',
        ]);

        $content->update($request->all());

        return response()->json(['message' => 'Updated successfully']);
    }

    public function destroy($id)
    {
        $content = CurriculumContent::findOrFail($id);
        $this->authorizeAccess($content); // optional

        $content->delete();

        return response()->json(['message' => 'Deleted']);
    }

    // Optional: allow only admin or owner
    protected function authorizeAccess($content)
    {
        if (Auth::user()->role !== 'admin' && $content->created_by !== Auth::id()){
            abort(403, 'Unauthorized');
        }
    }

}
