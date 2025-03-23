<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LeadImportRequest;
use App\Http\Requests\LeadRowRequest;
use App\Models\Lead;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use PhpOffice\PhpSpreadsheet\IOFactory;

class LeadImportController extends Controller
{
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(LeadImportRequest $request)
    {
        $file = $request->file('file');

        if (! $file->isValid() || $file->getClientMimeType() !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
            return back()->withErrors(['file' => 'Invalid file format. Please upload an Excel file.']);
        }

        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            return back()->withErrors(['file' => 'Failed to read the Excel file.']);
        }

        $errors = new MessageBag;

        foreach ($rows as $index => $row) {
            if ($index == 0) {
                continue;
            }

            $data = [
                'name' => $row[0] ?? '',
                'email' => $row[1] ?? '',
                'phone' => $row[2] ?? '',
                'status' => $row[3] ?? 'New',
            ];

            $validator = Validator::make($data, (new LeadRowRequest)->rules());

            if ($validator->fails()) {
                $errors->add("row_$index", 'Row '.($index + 1).' failed: '.implode(', ', $validator->errors()->all()));
            } else {
                Lead::create($data);
            }
        }

        if ($errors->isNotEmpty()) {
            return back()->withErrors($errors);
        }

        return back()->with('success', 'Leads imported successfully.');
    }
}
