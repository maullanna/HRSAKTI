<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Role;
use App\Http\Requests\EmployeeRec;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EmployeeController extends Controller
{

    public function index()
    {

        return view('admin.master-data.employees.index')->with(['employees' => Employee::all()]);
    }

    public function create()
    {
        return view('admin.master-data.employees.add_employee');
    }

    public function show(Employee $employee)
    {
        return view('admin.master-data.employees.edit_delete_employee', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        return view('admin.master-data.employees.edit_delete_employee', compact('employee'));
    }

    public function store(EmployeeRec $request)
    {
        $request->validated();

        $employee = new Employee;
        $employee->name = $request->name;
        $employee->employee_code = $request->employee_code;
        $employee->position = $request->position;
        $employee->email = $request->email;

        // Add new fields
        $employee->nik = $request->nik;
        $employee->tanggal_lahir = $request->tanggal_lahir;
        $employee->pendidikan = $request->pendidikan;
        $employee->kontrak_kerja = $request->kontrak_kerja;
        $employee->phone = $request->phone;
        $employee->hire_date = $request->hire_date;

        // Set kontrak_durasi only if kontrak_kerja needs duration
        $perluDurasi = ['Magang', 'Kontrak', 'PKL', 'Freelance'];
        $employee->kontrak_durasi = in_array($request->kontrak_kerja, $perluDurasi)
            ? $request->kontrak_durasi
            : null;

        if ($request->filled('status')) {
            $employee->status = $request->status;
        }

        if ($request->filled('pin_code')) {
            $employee->pin_code = bcrypt($request->pin_code);
        }

        // Set organizational fields
        $employee->id_section = $request->id_section;
        $employee->id_wadir_employee = $request->id_wadir_employee;
        $employee->id_sdm_employee = $request->id_sdm_employee;
        $employee->id_director_employee = $request->id_director_employee;
        $employee->id_section_employee = $request->id_section_employee;

        $employee->save();

        return redirect()->route('employees.index')->with('success', 'Employee Record has been created successfully !');
    }


    public function update(EmployeeRec $request, Employee $employee)
    {
        $request->validated();

        $employee->name = $request->name;
        $employee->employee_code = $request->employee_code;
        $employee->position = $request->position;
        $employee->email = $request->email;

        // Update fields - always update (can be empty/null)
        // Helper function to convert empty strings to null
        $toNull = function ($value) {
            return ($value === '' || $value === null) ? null : $value;
        };

        if (Schema::hasColumn('employees', 'nik')) {
            $employee->nik = $toNull($request->input('nik'));
        }
        if (Schema::hasColumn('employees', 'tanggal_lahir')) {
            $employee->tanggal_lahir = $toNull($request->input('tanggal_lahir'));
        }
        if (Schema::hasColumn('employees', 'pendidikan')) {
            $employee->pendidikan = $toNull($request->input('pendidikan'));
        }
        if (Schema::hasColumn('employees', 'kontrak_kerja')) {
            $employee->kontrak_kerja = $toNull($request->input('kontrak_kerja'));

            // Set kontrak_durasi only if kontrak_kerja needs duration
            if (Schema::hasColumn('employees', 'kontrak_durasi')) {
                $perluDurasi = ['Magang', 'Kontrak', 'PKL', 'Freelance'];
                if (in_array($request->kontrak_kerja, $perluDurasi)) {
                    $employee->kontrak_durasi = $toNull($request->input('kontrak_durasi'));
                } else {
                    $employee->kontrak_durasi = null;
                }
            }
        }

        // Update phone - always update (can be empty)
        if (Schema::hasColumn('employees', 'phone')) {
            $employee->phone = $toNull($request->input('phone'));
        }

        // Update hire_date - always update (can be empty)
        // hire_date might come from either hire_date or hire_date_tetap input (both have name="hire_date")
        if (Schema::hasColumn('employees', 'hire_date')) {
            $employee->hire_date = $toNull($request->input('hire_date'));
        }

        if ($request->filled('status')) {
            $employee->status = $request->status;
        }

        // Only update pin_code if provided
        if ($request->filled('pin_code')) {
            $employee->pin_code = bcrypt($request->pin_code);
        }

        // Update organizational fields
        if (Schema::hasColumn('employees', 'id_section')) {
            $employee->id_section = $toNull($request->input('id_section'));
        }
        if (Schema::hasColumn('employees', 'id_wadir_employee')) {
            $employee->id_wadir_employee = $toNull($request->input('id_wadir_employee'));
        }
        if (Schema::hasColumn('employees', 'id_sdm_employee')) {
            $employee->id_sdm_employee = $toNull($request->input('id_sdm_employee'));
        }
        if (Schema::hasColumn('employees', 'id_director_employee')) {
            $employee->id_director_employee = $toNull($request->input('id_director_employee'));
        }
        if (Schema::hasColumn('employees', 'id_section_employee')) {
            $employee->id_section_employee = $toNull($request->input('id_section_employee'));
        }

        $employee->save();

        return redirect()->route('employees.index')->with('success', 'Employee Record has been Updated successfully !');
    }


    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee Record has been Deleted successfully !');
    }

    public function showImportForm()
    {
        return view('admin.master-data.employees.import');
    }

    public function importEmployees(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx|max:5120' // 5MB max, only XLSX
        ]);

        try {
            $file = $request->file('import_file');

            // Only read Excel file (XLSX)
            $data = $this->readExcelFile($file);

            if (empty($data)) {
                return redirect()->route('employees.index')->with('error', 'File kosong atau tidak dapat dibaca.');
            }

            // Validate data
            $validationResult = $this->validateEmployeeImportData($data);
            if (!$validationResult['valid']) {
                $errorMessage = 'Data tidak valid: ' . implode('; ', array_slice($validationResult['errors'], 0, 10));
                if (count($validationResult['errors']) > 10) {
                    $errorMessage .= ' dan ' . (count($validationResult['errors']) - 10) . ' error lainnya';
                }
                return redirect()->route('employees.index')->with('error', $errorMessage);
            }

            // Process import
            $importResult = $this->processEmployeeImport($data);

            $message = "Import selesai! {$importResult['success']} data berhasil diimpor";
            if ($importResult['failed'] > 0) {
                $message .= ", {$importResult['failed']} data gagal";
            }
            if (!empty($importResult['errors'])) {
                $message .= '. Detail Error: ' . implode('; ', array_slice($importResult['errors'], 0, 5));
                if (count($importResult['errors']) > 5) {
                    $message .= ' dan ' . (count($importResult['errors']) - 5) . ' error lainnya';
                }
            }

            return redirect()->route('employees.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('employees.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
            // Skip rows that contain "Catatan:" or instruction text
            for ($row = 2; $row <= $highestRow; $row++) {
                $firstCell = $sheet->getCell('A' . $row)->getValue();

                // Skip instruction rows
                if (
                    stripos((string)$firstCell, 'Catatan') !== false ||
                    stripos((string)$firstCell, 'Kolom wajib') !== false ||
                    stripos((string)$firstCell, 'Format tanggal') !== false ||
                    stripos((string)$firstCell, 'Posisi valid') !== false
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

    private function validateEmployeeImportData($data)
    {
        $errors = [];
        $valid = true;

        foreach ($data as $index => $row) {
            $rowNumber = $index + 2; // +2 because we skip header and arrays start at 0

            // Required fields
            if (empty($row['name']) && empty($row['employee_code'])) {
                $errors[] = "Baris {$rowNumber}: Name atau Employee Code harus diisi";
                $valid = false;
            }

            if (empty($row['employee_code'])) {
                $errors[] = "Baris {$rowNumber}: Employee Code wajib diisi";
                $valid = false;
            } else {
                // Check if employee_code already exists
                $existing = Employee::where('employee_code', $row['employee_code'])->first();
                if ($existing) {
                    $errors[] = "Baris {$rowNumber}: Employee Code '{$row['employee_code']}' sudah ada di database";
                    $valid = false;
                }
            }

            if (empty($row['name'])) {
                $errors[] = "Baris {$rowNumber}: Name wajib diisi";
                $valid = false;
            }

            if (empty($row['position'])) {
                $errors[] = "Baris {$rowNumber}: Position wajib diisi";
                $valid = false;
            } else {
                $validPositions = ['Director', 'YTI Board of Directors', 'Wadir 1', 'Wadir 2', 'Section Prodi TPMO', 'Section Prodi TOPKR4', 'Section BAAK', 'Section Teaching Factory', 'Section IT & Sarpras', 'Section Administrasi', 'SDM/HRD', 'Employees', 'Magang', 'PKL'];
                if (!in_array($row['position'], $validPositions)) {
                    $errors[] = "Baris {$rowNumber}: Position '{$row['position']}' tidak valid";
                    $valid = false;
                }
            }

            // Optional fields validation
            if (!empty($row['email'])) {
                if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Baris {$rowNumber}: Email '{$row['email']}' tidak valid";
                    $valid = false;
                } else {
                    $existingEmail = Employee::where('email', $row['email'])->first();
                    if ($existingEmail) {
                        $errors[] = "Baris {$rowNumber}: Email '{$row['email']}' sudah digunakan";
                        $valid = false;
                    }
                }
            }

            if (!empty($row['tanggal_lahir'])) {
                try {
                    \Carbon\Carbon::createFromFormat('Y-m-d', $row['tanggal_lahir']);
                } catch (\Exception $e) {
                    try {
                        \Carbon\Carbon::createFromFormat('d/m/Y', $row['tanggal_lahir']);
                    } catch (\Exception $e2) {
                        $errors[] = "Baris {$rowNumber}: Format tanggal_lahir tidak valid. Gunakan YYYY-MM-DD atau DD/MM/YYYY";
                        $valid = false;
                    }
                }
            }

            if (!empty($row['pendidikan'])) {
                $validPendidikan = ['SD', 'SMP', 'SMA/SMK', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'];
                if (!in_array($row['pendidikan'], $validPendidikan)) {
                    $errors[] = "Baris {$rowNumber}: Pendidikan '{$row['pendidikan']}' tidak valid";
                    $valid = false;
                }
            }

            if (!empty($row['kontrak_kerja'])) {
                $validKontrak = ['Tetap', 'Kontrak', 'Magang', 'PKL', 'Freelance'];
                if (!in_array($row['kontrak_kerja'], $validKontrak)) {
                    $errors[] = "Baris {$rowNumber}: Kontrak Kerja '{$row['kontrak_kerja']}' tidak valid";
                    $valid = false;
                }
            }

            if (!empty($row['hire_date'])) {
                try {
                    \Carbon\Carbon::createFromFormat('Y-m-d', $row['hire_date']);
                } catch (\Exception $e) {
                    try {
                        \Carbon\Carbon::createFromFormat('d/m/Y', $row['hire_date']);
                    } catch (\Exception $e2) {
                        $errors[] = "Baris {$rowNumber}: Format hire_date tidak valid. Gunakan YYYY-MM-DD atau DD/MM/YYYY";
                        $valid = false;
                    }
                }
            }

            // Validate basic_salary
            if (!empty($row['basic_salary'])) {
                $basicSalary = str_replace(',', '', $row['basic_salary']);
                if (!is_numeric($basicSalary) || $basicSalary < 0) {
                    $errors[] = "Baris {$rowNumber}: Basic Salary harus berupa angka positif";
                    $valid = false;
                }
            }

            // Validate net_salary
            if (!empty($row['net_salary'])) {
                $netSalary = str_replace(',', '', $row['net_salary']);
                if (!is_numeric($netSalary) || $netSalary < 0) {
                    $errors[] = "Baris {$rowNumber}: Net Salary harus berupa angka positif";
                    $valid = false;
                }
            }
        }

        return ['valid' => $valid, 'errors' => $errors];
    }

    private function processEmployeeImport($data)
    {
        $success = 0;
        $failed = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($data as $index => $row) {
                try {
                    $employee = new Employee();

                    // Required fields
                    $employee->name = $row['name'];
                    $employee->employee_code = $row['employee_code'];
                    $employee->position = $row['position'];

                    // Optional fields
                    $employee->email = !empty($row['email']) ? $row['email'] : null;
                    $employee->phone = !empty($row['phone']) ? $row['phone'] : null;
                    $employee->nik = !empty($row['nik']) ? $row['nik'] : null;

                    // Date fields
                    if (!empty($row['tanggal_lahir'])) {
                        try {
                            $employee->tanggal_lahir = \Carbon\Carbon::createFromFormat('Y-m-d', $row['tanggal_lahir'])->format('Y-m-d');
                        } catch (\Exception $e) {
                            $employee->tanggal_lahir = \Carbon\Carbon::createFromFormat('d/m/Y', $row['tanggal_lahir'])->format('Y-m-d');
                        }
                    } else {
                        $employee->tanggal_lahir = null;
                    }

                    if (!empty($row['hire_date'])) {
                        try {
                            $employee->hire_date = \Carbon\Carbon::createFromFormat('Y-m-d', $row['hire_date'])->format('Y-m-d');
                        } catch (\Exception $e) {
                            $employee->hire_date = \Carbon\Carbon::createFromFormat('d/m/Y', $row['hire_date'])->format('Y-m-d');
                        }
                    } else {
                        $employee->hire_date = null;
                    }

                    // Enum fields
                    $employee->pendidikan = !empty($row['pendidikan']) ? $row['pendidikan'] : null;
                    $employee->kontrak_kerja = !empty($row['kontrak_kerja']) ? $row['kontrak_kerja'] : 'Tetap';

                    // Kontrak durasi
                    $perluDurasi = ['Magang', 'Kontrak', 'PKL', 'Freelance'];
                    if (in_array($employee->kontrak_kerja, $perluDurasi)) {
                        $employee->kontrak_durasi = !empty($row['kontrak_durasi']) ? (int)$row['kontrak_durasi'] : null;
                    } else {
                        $employee->kontrak_durasi = null;
                    }

                    // Status
                    $employee->status = !empty($row['status']) && in_array(strtolower($row['status']), ['active', 'inactive'])
                        ? strtolower($row['status'])
                        : 'active';

                    // PIN Code
                    if (!empty($row['pin_code'])) {
                        $employee->pin_code = bcrypt($row['pin_code']);
                    }

                    // Basic Salary
                    if (!empty($row['basic_salary'])) {
                        $employee->basic_salary = (float)str_replace(',', '', $row['basic_salary']);
                    }

                    // Net Salary (jika ada kolom di database)
                    if (!empty($row['net_salary']) && Schema::hasColumn('employees', 'net_salary')) {
                        $employee->net_salary = (float)str_replace(',', '', $row['net_salary']);
                    }

                    $employee->save();
                    $success++;
                } catch (\Exception $e) {
                    $failed++;
                    $rowNumber = $index + 2;
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
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

    public function downloadEmployeeTemplate()
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set title
            $sheet->setTitle('Employee Import Template');

            // Header row dengan nama field database (untuk import compatibility)
            $headers = [
                'employee_code',
                'name',
                'nik',
                'tanggal_lahir',
                'pendidikan',
                'kontrak_kerja',
                'kontrak_durasi',
                'position',
                'email',
                'phone',
                'hire_date',
                'basic_salary',
                'net_salary',
                'status',
                'pin_code'
            ];

            // Header display names untuk styling
            $headerDisplay = [
                'Employee Code',
                'Name',
                'NIK',
                'Tanggal Lahir',
                'Pendidikan',
                'Kontrak Kerja',
                'Kontrak Durasi',
                'Position',
                'Email',
                'Phone',
                'Hire Date',
                'Basic Salary',
                'Net Salary',
                'Status',
                'PIN Code'
            ];

            // Set header values (menggunakan field database names)
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

            $sheet->getStyle('A1:O1')->applyFromArray($headerStyle);
            $sheet->getRowDimension('1')->setRowHeight(30); // Increased height for better spacing

            // Set column widths dengan spacing yang lebih baik
            $columnWidths = [
                'A' => 18,  // Employee Code
                'B' => 25,  // Name
                'C' => 20,  // NIK
                'D' => 18,  // Tanggal Lahir
                'E' => 15,  // Pendidikan
                'F' => 18,  // Kontrak Kerja
                'G' => 18,  // Kontrak Durasi
                'H' => 30,  // Position
                'I' => 30,  // Email
                'J' => 18,  // Phone
                'K' => 18,  // Hire Date
                'L' => 18,  // Basic Salary
                'M' => 18,  // Net Salary
                'N' => 15,  // Status
                'O' => 15,  // PIN Code
            ];

            foreach ($columnWidths as $col => $width) {
                $sheet->getColumnDimension($col)->setWidth($width);
            }

            // Sample data row 1
            $row2 = [
                'EMP001',
                'John Doe',
                '3201234567890001',
                '1990-01-15',
                'S1',
                'Tetap',
                '',
                'Employees',
                'john.doe@example.com',
                '081234567890',
                '2024-01-01',
                '5000000',
                '5000000',
                'active',
                '1234'
            ];

            $column = 'A';
            foreach ($row2 as $value) {
                $sheet->setCellValue($column . '2', $value);
                $column++;
            }

            // Sample data row 2
            $row3 = [
                'EMP002',
                'Jane Smith',
                '3201234567890002',
                '1992-05-20',
                'S1',
                'Kontrak',
                '6',
                'Magang',
                'jane.smith@example.com',
                '081234567891',
                '2024-02-01',
                '4000000',
                '4000000',
                'active',
                '5678'
            ];

            $column = 'A';
            foreach ($row3 as $value) {
                $sheet->setCellValue($column . '3', $value);
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

            $sheet->getStyle('A2:O3')->applyFromArray($dataStyle);
            $sheet->getRowDimension('2')->setRowHeight(25); // Increased height
            $sheet->getRowDimension('3')->setRowHeight(25); // Increased height

            // Alternating row colors untuk readability
            $sheet->getStyle('A2:O2')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('FFFFFF');

            $sheet->getStyle('A3:O3')->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('F2F2F2');

            // Freeze header row
            $sheet->freezePane('A2');

            // Add spacing row
            $sheet->getRowDimension('4')->setRowHeight(5);

            // Add note/instruction row dengan styling yang lebih baik
            $sheet->setCellValue('A5', 'Catatan:');
            $sheet->mergeCells('A5:O5');
            $noteHeaderStyle = [
                'font' => [
                    'bold' => true,
                    'size' => 11,
                    'color' => ['rgb' => '856404'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF3CD'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'FFE69C'],
                    ],
                ],
            ];
            $sheet->getStyle('A5')->applyFromArray($noteHeaderStyle);
            $sheet->getRowDimension('5')->setRowHeight(22);

            $sheet->setCellValue('A6', '1. Kolom wajib: Employee Code, Name, Position');
            $sheet->mergeCells('A6:O6');
            $sheet->setCellValue('A7', '2. Format tanggal: YYYY-MM-DD atau DD/MM/YYYY');
            $sheet->mergeCells('A7:O7');
            $sheet->setCellValue('A8', '3. Basic Salary dan Net Salary: Format angka (contoh: 5000000 untuk 5 juta)');
            $sheet->mergeCells('A8:O8');
            $sheet->setCellValue('A9', '4. Posisi valid: Director, Wadir 1, Wadir 2, Section Prodi TPMO, Section Prodi TOPKR4, Section BAAK, Section Teaching Factory, Section IT & Sarpras, Section Administrasi, SDM/HRD, YTI Board of Directors, Employees, Magang, PKL');
            $sheet->mergeCells('A9:O9');

            $noteStyle = [
                'font' => [
                    'size' => 10,
                    'color' => ['rgb' => '856404'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFFBF0'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'FFE69C'],
                    ],
                ],
            ];
            $sheet->getStyle('A6:A9')->applyFromArray($noteStyle);
            $sheet->getRowDimension('6')->setRowHeight(20);
            $sheet->getRowDimension('7')->setRowHeight(20);
            $sheet->getRowDimension('8')->setRowHeight(20);
            $sheet->getRowDimension('9')->setRowHeight(25);

            // Create writer
            $writer = new Xlsx($spreadsheet);

            // Save to temporary file
            $filename = 'employee_import_template.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), 'employee_template');
            $writer->save($tempFile);

            // Return file download
            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->route('employees.index')
                ->with('error', 'Gagal membuat template Excel: ' . $e->getMessage());
        }
    }

    /**
     * Check if employee code, email, or nik already exists
     */
    public function checkUnique(Request $request)
    {
        $field = $request->input('field');
        $value = trim($request->input('value'));
        $excludeId = $request->input('exclude_id'); // For edit form

        if (!in_array($field, ['employee_code', 'email', 'nik'])) {
            return response()->json(['error' => 'Invalid field'], 400);
        }

        // If value is empty and field is optional (email, nik), return available
        if (empty($value) && in_array($field, ['email', 'nik'])) {
            return response()->json([
                'exists' => false,
                'message' => "This {$field} is available."
            ]);
        }

        // If value is empty and field is required (employee_code), return exists to show error
        if (empty($value) && $field === 'employee_code') {
            return response()->json([
                'exists' => true,
                'message' => "Employee code is required."
            ]);
        }

        $query = \App\Models\Employee::where($field, $value);

        // Exclude current employee when editing
        if ($excludeId) {
            $query->where('id_employees', '!=', $excludeId);
        }

        $exists = $query->exists();

        $messages = [
            'employee_code' => $exists ? 'This employee code is already registered.' : 'This employee code is available.',
            'email' => $exists ? 'This email is already registered.' : 'This email is available.',
            'nik' => $exists ? 'This NIK is already registered.' : 'This NIK is available.'
        ];

        return response()->json([
            'exists' => $exists,
            'message' => $messages[$field] ?? "This {$field} is " . ($exists ? 'already registered.' : 'available.')
        ]);
    }
}
