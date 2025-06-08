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
            $data = CurriculumContent::all();
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

        $uploadedFiles = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $originalName = $file->getClientOriginalName();
                $file->move(public_path('curriculum'), $originalName);
                $url = asset('curriculum/' . $originalName);
                $uploadedFiles[] = $url;
            }
        }

        return CurriculumContent::create([
            'title' => $request->title,
            'description' => $request->description,
            'peo_ids' => $request->peo_ids,
            'files' => $uploadedFiles,
            'created_by' => Auth::id(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $content = CurriculumContent::findOrFail($id);
        $this->authorizeAccess($content);

        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'peo_ids' => 'required|array',
        ]);

        $uploadedFiles = $content->files ?? [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $originalName = $file->getClientOriginalName();
                $file->move(public_path('curriculum'), $originalName);
                $url = asset('curriculum/' . $originalName);
                $uploadedFiles[] = $url;
            }
        }

        $content->update([
            'title' => $request->title,
            'description' => $request->description,
            'peo_ids' => $request->peo_ids,
            'files' => $uploadedFiles,
        ]);

        return response()->json(['message' => 'Updated successfully']);
    }

    public function destroy($id)
    {
        $content = CurriculumContent::findOrFail($id);
        $this->authorizeAccess($content);
        $content->delete();
        return response()->json(['message' => 'Deleted']);
    }

    protected function authorizeAccess($content)
    {
        if (Auth::user()->role !== 'admin' && $content->created_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }
}
