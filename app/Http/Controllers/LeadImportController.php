<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LeadImportRequest;
use App\Http\Requests\LeadRowRequest;
use App\Models\Lead;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;

class LeadImportController extends Controller
{
    /**
     * @param LeadImportRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(LeadImportRequest $request)
    {
        $file = $request->file('file');

        if (!$file->isValid() || $file->getClientMimeType() !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
            return response()->json(['message' => 'Invalid file format. Please upload an Excel file.'], 400);
        }

        try {
            try {
                $spreadsheet = IOFactory::load($file->getPathname());
            } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                return response()->json(['message' => 'Failed to read the Excel file.', 'error' => $e->getMessage()], 400);
            }
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            return response()->json(['message' => 'Failed to read the Excel file.'], 400);
        }

        $errors = [];

        foreach ($rows as $index => $row) {
            if ($index == 0) continue;

            $data = [
                'name' => $row[0] ?? '',
                'email' => $row[1] ?? '',
                'phone' => $row[2] ?? '',
                'status' => $row[3] ?? 'New',
            ];
            $validator = Validator::make($data, (new LeadRowRequest())->rules());

            if ($validator->fails()) {
                $errors[] = "Row " . ($index + 1) . " failed: " . implode(", ", $validator->errors()->all());
            } else {
                Lead::create($data);
            }
        }

        return response()->json([
            'message' => 'Import completed.',
            'errors' => $errors
        ], 200);
    }
}
