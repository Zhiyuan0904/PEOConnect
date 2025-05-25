<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PEO;

class PEOController extends Controller
{
    public function index()
    {
        try {
            return response()->json(PEO::all());
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'PEO Load Error',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:10|unique:peos,code',
            'description' => 'nullable|string|max:255',
        ]);

        return response()->json(PEO::create($data), 201);
    }

    public function update(Request $request, $id)
    {
        $peo = PEO::findOrFail($id);

        $data = $request->validate([
            'code' => 'required|string|max:10|unique:peos,code,' . $id,
            'description' => 'nullable|string|max:255',
        ]);

        $peo->update($data);

        return response()->json($peo);
    }

    public function destroy($id)
    {
        $peo = PEO::findOrFail($id);
        $peo->delete();

        return response()->noContent();
    }
}
