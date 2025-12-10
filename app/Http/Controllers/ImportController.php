<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportController extends Controller
{
    public function showImportForm()
    {
        return view('admin.salaries.import');
    }

    public function importSalaries(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx|max:2048' // Only XLSX
        ]);

        try {
            $file = $request->file('import_file');
            
            // Only read Excel file (XLSX)
            $data = $this->readExcelFile($file);

            if (empty($data)) {
                return back()->with('error', 'File kosong atau tidak dapat dibaca.');
            }

            // Validate data
            $validationResult = $this->validateImportData($data);
            if (!$validationResult['valid']) {
                return back()->with('error', 'Data tidak valid: ' . implode(', ', $validationResult['errors']));
            }

            // Process import
            $importResult = $this->processImport($data);

            return back()->with('success', 
                "Import berhasil! {$importResult['success']} data berhasil diimpor, {$importResult['failed']} data gagal."
            );

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function readExcelFile($file)
    {
        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $data = [];

            // Get highest row and column
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            if ($highestRow < 2) {
                return [];
            }

            // Read header row (first row)
            $header = [];
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $cellValue = $sheet->getCell($col . '1')->getValue();
                if ($cellValue) {
                    $header[] = strtolower(trim($cellValue));
                } else {
                    break;
                }
            }

            if (empty($header)) {
                return [];
            }

            // Read data rows (starting from row 2)
            for ($row = 2; $row <= $highestRow; $row++) {
                $firstCell = $sheet->getCell('A' . $row)->getValue();

                // Skip instruction rows
                if (
                    stripos((string)$firstCell, 'Catatan') !== false ||
                    stripos((string)$firstCell, 'Kolom wajib') !== false ||
                    stripos((string)$firstCell, 'Format') !== false
                ) {
                    continue;
                }

                $rowData = [];
                $hasData = false;

                $colIndex = 0;
                for ($col = 'A'; $col <= $highestColumn && $colIndex < count($header); $col++) {
                    $cellValue = $sheet->getCell($col . $row)->getValue();

                    // Handle formula results
                    if ($cellValue instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
                        $cellValue = $cellValue->getPlainText();
                    }

                    $rowData[$header[$colIndex]] = $cellValue ? trim((string)$cellValue) : '';

                    if (!empty($rowData[$header[$colIndex]])) {
                        $hasData = true;
                    }

                    $colIndex++;
                }

                // Only add row if it has at least some data
                if ($hasData && count($rowData) > 0) {
                    $data[] = $rowData;
                }
            }

            return $data;
        } catch (\Exception $e) {
            throw new \Exception('Gagal membaca file Excel: ' . $e->getMessage());
        }
    }

    private function validateImportData($data)
    {
        $errors = [];
        $valid = true;

        foreach ($data as $index => $row) {
            $rowNumber = $index + 2; // +2 because we skip header and arrays start at 0

            // Validate employee_id
            if (empty($row['employee_id'])) {
                $errors[] = "Baris {$rowNumber}: Employee ID tidak boleh kosong";
                $valid = false;
            } else {
                $employee = Employee::find($row['employee_id']);
                if (!$employee) {
                    $errors[] = "Baris {$rowNumber}: Employee ID {$row['employee_id']} tidak ditemukan";
                    $valid = false;
                }
            }

            // Validate month
            if (empty($row['month'])) {
                $errors[] = "Baris {$rowNumber}: Bulan tidak boleh kosong";
                $valid = false;
            } else {
                try {
                    Carbon::createFromFormat('Y-m', $row['month']);
                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: Format bulan tidak valid. Gunakan format YYYY-MM (contoh: 2024-01)";
                    $valid = false;
                }
            }

            // Validate basic_salary
            if (empty($row['basic_salary']) || !is_numeric($row['basic_salary']) || $row['basic_salary'] < 0) {
                $errors[] = "Baris {$rowNumber}: Basic salary harus berupa angka positif";
                $valid = false;
            }

            // Validate allowances (optional)
            if (!empty($row['allowances'])) {
                $allowances = $this->parseJsonField($row['allowances']);
                if ($allowances === null) {
                    $errors[] = "Baris {$rowNumber}: Format allowances tidak valid. Gunakan format JSON";
                    $valid = false;
                }
            }

            // Validate deductions (optional)
            if (!empty($row['deductions'])) {
                $deductions = $this->parseJsonField($row['deductions']);
                if ($deductions === null) {
                    $errors[] = "Baris {$rowNumber}: Format deductions tidak valid. Gunakan format JSON";
                    $valid = false;
                }
            }
        }

        return ['valid' => $valid, 'errors' => $errors];
    }

    private function parseJsonField($field)
    {
        if (empty($field)) {
            return [];
        }

        // Try to parse as JSON
        $decoded = json_decode($field, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        // Try to parse as key:value pairs separated by semicolons
        $pairs = explode(';', $field);
        $result = [];
        
        foreach ($pairs as $pair) {
            if (strpos($pair, ':') !== false) {
                list($key, $value) = explode(':', $pair, 2);
                $result[trim($key)] = (float)trim($value);
            }
        }

        return empty($result) ? null : $result;
    }

    private function processImport($data)
    {
        $success = 0;
        $failed = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($data as $row) {
                try {
                    // Check if salary already exists for this employee and month
                    $existingSalary = Salary::where('employee_id', $row['employee_id'])
                        ->where('month', $row['month'] . '-01') // Convert to full date
                        ->first();

                    if ($existingSalary) {
                        // Update existing record
                        $existingSalary->basic_salary = $row['basic_salary'];
                        $existingSalary->allowances = $this->parseJsonField($row['allowances']) ?: [];
                        $existingSalary->deductions = $this->parseJsonField($row['deductions']) ?: [];
                        
                        // Recalculate net salary
                        $totalAllowances = array_sum($existingSalary->allowances);
                        $totalDeductions = array_sum($existingSalary->deductions);
                        $existingSalary->net_salary = $existingSalary->basic_salary + $totalAllowances - $totalDeductions;
                        
                        $existingSalary->save();
                    } else {
                        // Create new record
                        $salary = new Salary();
                        $salary->employee_id = $row['employee_id'];
                        $salary->month = $row['month'] . '-01'; // Convert to full date
                        $salary->basic_salary = $row['basic_salary'];
                        $salary->allowances = $this->parseJsonField($row['allowances']) ?: [];
                        $salary->deductions = $this->parseJsonField($row['deductions']) ?: [];
                        
                        // Calculate net salary
                        $totalAllowances = array_sum($salary->allowances);
                        $totalDeductions = array_sum($salary->deductions);
                        $salary->net_salary = $salary->basic_salary + $totalAllowances - $totalDeductions;
                        
                        $salary->save();
                    }

                    $success++;

                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Gagal memproses data untuk Employee ID {$row['employee_id']}: " . $e->getMessage();
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

        return [
            'success' => $success,
            'failed' => $failed,
            'errors' => $errors
        ];
    }

    public function downloadTemplate()
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set title
            $sheet->setTitle('Salary Import Template');

            // Header row
            $headers = [
                'employee_id',
                'month',
                'basic_salary',
                'allowances',
                'deductions'
            ];

            $headerDisplay = [
                'Employee ID',
                'Month (YYYY-MM)',
                'Basic Salary',
                'Allowances (JSON)',
                'Deductions (JSON)'
            ];

            // Set header values
            $column = 'A';
            foreach ($headers as $index => $header) {
                $sheet->setCellValue($column . '1', $header);
                $column++;
            }

            // Style untuk header dengan warna yang lebih menarik
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2E75B6'], // Blue color
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['rgb' => '1F4E78'],
                    ],
                ],
            ];

            $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
            $sheet->getRowDimension('1')->setRowHeight(30);

            // Set column widths dengan spacing yang lebih baik
            $columnWidths = [
                'A' => 18,  // Employee ID
                'B' => 20,  // Month
                'C' => 18,  // Basic Salary
                'D' => 35,  // Allowances
                'E' => 35,  // Deductions
            ];

            foreach ($columnWidths as $col => $width) {
                $sheet->getColumnDimension($col)->setWidth($width);
            }

            // Sample data row
            $row2 = [
                '1',
                '2024-01',
                '5000000',
                '{"transport": 500000, "meal": 300000}',
                '{"tax": 500000, "insurance": 200000}'
            ];

            $column = 'A';
            foreach ($row2 as $value) {
                $sheet->setCellValue($column . '2', $value);
                $column++;
            }

            // Style untuk data rows dengan spacing yang lebih baik
            $dataStyle = [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D0D0D0'],
                    ],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFFFFF'],
                ],
            ];

            $sheet->getStyle('A2:E2')->applyFromArray($dataStyle);
            $sheet->getRowDimension('2')->setRowHeight(25);

            // Freeze header row
            $sheet->freezePane('A2');

            // Add note/instruction row
            $sheet->setCellValue('A3', 'Catatan:');
            $sheet->mergeCells('A3:E3');
            $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(11);
            $sheet->getStyle('A3')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('FFF3CD');

            $sheet->setCellValue('A4', '1. Kolom wajib: Employee ID, Month, Basic Salary');
            $sheet->mergeCells('A4:E4');
            $sheet->setCellValue('A5', '2. Format month: YYYY-MM (contoh: 2024-01)');
            $sheet->mergeCells('A5:E5');
            $sheet->setCellValue('A6', '3. Allowances dan Deductions: Format JSON (opsional)');
            $sheet->mergeCells('A6:E6');
            $sheet->getStyle('A4:A6')->getFont()->setSize(10);
            $sheet->getRowDimension('3')->setRowHeight(20);
            $sheet->getRowDimension('4')->setRowHeight(18);
            $sheet->getRowDimension('5')->setRowHeight(18);
            $sheet->getRowDimension('6')->setRowHeight(18);

            // Create writer
            $writer = new Xlsx($spreadsheet);

            // Save to temporary file
            $filename = 'salary_import_template.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), 'salary_template');
            $writer->save($tempFile);

            // Return file download
            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal membuat template Excel: ' . $e->getMessage());
        }
    }
}
