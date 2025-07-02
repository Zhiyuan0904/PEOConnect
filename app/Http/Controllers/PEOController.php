<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PEO;
use Illuminate\Support\Facades\Validator;

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

    // ✅ NEW FUNCTION: Bulk CSV Upload
    public function bulkUpload(Request $request)
    {
        $file = $request->file('csv');
        $data = array_map('str_getcsv', file($file));

        // Skip header if present
        if (strtolower(trim($data[0][0])) === 'description') {
            array_shift($data);
        }

        // Get current max PEO number
        $existingCodes = PEO::pluck('code')->toArray();
        $existingNumbers = array_map(function ($code) {
            if (preg_match('/PEO(\d+)/', $code, $matches)) {
                return (int)$matches[1];
            }
            return 0;
        }, $existingCodes);

        $maxNumber = $existingNumbers ? max($existingNumbers) : 0;

        $imported = 0;

        foreach ($data as $row) {
            $description = $row[0] ?? null;
            if (!$description) continue;

            $maxNumber++;
            $code = 'PEO' . $maxNumber;

            PEO::create([
                'code' => $code,
                'description' => $description,
            ]);

            $imported++;
        }

        return response()->json([
            'imported' => $imported,
            'skipped' => count($data) - $imported
        ]);
    }

}
