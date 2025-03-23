<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GenerateTestExcel extends Command
{
    protected $signature = 'generate:test-excel';
    protected $description = 'Generate a test Excel file for import testing';

    public function handle()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Name');
        $sheet->setCellValue('B1', 'Email');
        $sheet->setCellValue('C1', 'Phone');
        $sheet->setCellValue('D1', 'Status');

        $data = [
            ['John Doe', 'john@example.com', '1234567890', 'New'],
            ['Jane Smith', 'jane@example.com', '9876543210', 'Pending'],
            ['Alice Brown', 'alice@example.com', '5551234567', 'Completed'],
        ];

        $rowIndex = 2;
        foreach ($data as $row) {
            $sheet->setCellValue("A$rowIndex", $row[0]);
            $sheet->setCellValue("B$rowIndex", $row[1]);
            $sheet->setCellValue("C$rowIndex", $row[2]);
            $sheet->setCellValue("D$rowIndex", $row[3]);
            $rowIndex++;
        }

        $filePath = storage_path('app/test.xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        $this->info("Test Excel file created at: $filePath");
    }
}
