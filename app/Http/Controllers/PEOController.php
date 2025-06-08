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
        $request->validate([
            'csv' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv');
        $path = $file->getRealPath();
        $handle = fopen($path, 'r');

        $header = fgetcsv($handle); // read header row
        $imported = 0;
        $skipped = 0;

        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $row = array_combine($header, $data);

            // Validate each row before insert
            $validator = Validator::make($row, [
                'code' => 'required|string|max:10|unique:peos,code',
                'description' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                $skipped++;
                continue; // skip invalid rows
            }

            PEO::create([
                'code' => $row['code'],
                'description' => $row['description'],
            ]);

            $imported++;
        }

        fclose($handle);

        return response()->json([
            'message' => 'CSV import completed.',
            'imported' => $imported,
            'skipped' => $skipped,
        ]);
    }
}
