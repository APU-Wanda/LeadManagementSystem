<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class LeadImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_import()
    {
        // Fake storage to simulate file upload
        Storage::fake('local');

        // ✅ Create a real Excel file and store it in the fake storage
        $filePath = storage_path('app/test.xlsx');
        $this->createExcelFile($filePath);

        // ✅ Convert the Excel file into an UploadedFile instance
        $file = new UploadedFile(
            $filePath,
            'test_leads.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        // ✅ Perform API request
        $response = $this->postJson('/api/import', ['file' => $file]);

        // ✅ Assert JSON response
        $response->assertJson([
            'message' => 'Import completed.',
        ])->assertStatus(200);
    }

    private function createExcelFile($filePath)
    {
        // Create a new spreadsheet
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers
        $sheet->setCellValue('A1', 'Name');
        $sheet->setCellValue('B1', 'Email');
        $sheet->setCellValue('C1', 'Phone');
        $sheet->setCellValue('D1', 'Status');

        // Add sample data
        $sheet->setCellValue('A2', 'John Doe');
        $sheet->setCellValue('B2', 'john@example.com');
        $sheet->setCellValue('C2', '1234567890');
        $sheet->setCellValue('D2', 'New');

        // Save the file
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
    }
}
