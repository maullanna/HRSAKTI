<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportController extends Controller
{
    public function showImportForm()
    {
        return view('admin.salaries.import');
    }

    public function importSalaries(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,xlsx,xls|max:2048'
        ]);

        try {
            $file = $request->file('import_file');
            $extension = $file->getClientOriginalExtension();
            
            // Read file based on extension
            if ($extension === 'csv') {
                $data = $this->readCsvFile($file);
            } else {
                $data = $this->readExcelFile($file);
            }

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

    private function readCsvFile($file)
    {
        $data = [];
        $handle = fopen($file->getPathname(), 'r');
        
        // Skip header row
        $header = fgetcsv($handle);
        
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 3) { // Minimum required columns
                $data[] = [
                    'employee_id' => trim($row[0]),
                    'month' => trim($row[1]),
                    'basic_salary' => trim($row[2]),
                    'allowances' => isset($row[3]) ? trim($row[3]) : '',
                    'deductions' => isset($row[4]) ? trim($row[4]) : '',
                ];
            }
        }
        
        fclose($handle);
        return $data;
    }

    private function readExcelFile($file)
    {
        // For Excel files, we'll use a simple approach
        // In production, you might want to use PhpSpreadsheet
        $data = [];
        
        // Move file to temp location
        $tempPath = $file->store('temp');
        $fullPath = storage_path('app/' . $tempPath);
        
        // Simple CSV conversion (this is basic - for production use PhpSpreadsheet)
        if (($handle = fopen($fullPath, 'r')) !== false) {
            $header = fgetcsv($handle);
            
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) >= 3) {
                    $data[] = [
                        'employee_id' => trim($row[0]),
                        'month' => trim($row[1]),
                        'basic_salary' => trim($row[2]),
                        'allowances' => isset($row[3]) ? trim($row[3]) : '',
                        'deductions' => isset($row[4]) ? trim($row[4]) : '',
                    ];
                }
            }
            fclose($handle);
        }
        
        // Clean up temp file
        unlink($fullPath);
        
        return $data;
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
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="salary_import_template.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'employee_id',
                'month',
                'basic_salary',
                'allowances',
                'deductions'
            ]);

            // Sample data row
            fputcsv($file, [
                '1',
                '2024-01',
                '5000000',
                '{"transport": 500000, "meal": 300000}',
                '{"tax": 500000, "insurance": 200000}'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
