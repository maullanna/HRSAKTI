<?php

namespace App\Http\Controllers;

use App\Models\Latetime;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        // Optimasi: Select only needed columns (include primary key untuk pagination)
        // Hanya ambil check-in (type = 0) sebagai record utama
        $query = Attendance::select([
            'id_attendance',
            'emp_id',
            'attendance_time',
            'attendance_date',
            'status',
            'type'
        ])->where('type', 0); // Hanya check-in (type = 0)

        // Filter berdasarkan tanggal
        if ($request->has('date_from') && $request->date_from) {
            $query->where('attendance_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('attendance_date', '<=', $request->date_to);
        }

        // Filter berdasarkan employee (untuk admin)
        if ($request->has('emp_id') && $request->emp_id) {
            $query->where('emp_id', $request->emp_id);
        }

        // Check if user is employee
        if (Auth::guard('employee')->check()) {
            $employee = Auth::guard('employee')->user();
            $query->where('emp_id', $employee->id_employees);
        }

        // Optimasi: Eager load hanya kolom yang diperlukan dari employee
        $query->with([
            'employee:id_employees,name',
            'employee.schedules:id_schedule,time_in,time_out'
        ]);

        // Optimasi: Gunakan simplePaginate untuk menghindari COUNT query yang berat
        // Jika butuh total count, bisa gunakan paginate() tapi akan lebih lambat
        $useSimplePagination = !$request->has('show_total') || $request->show_total != '1';

        if ($useSimplePagination) {
            $attendances = $query->orderBy('attendance_date', 'desc')
                ->orderBy('attendance_time', 'desc')
                ->simplePaginate(50);
        } else {
            $attendances = $query->orderBy('attendance_date', 'desc')
                ->orderBy('attendance_time', 'desc')
                ->paginate(50);
        }

        // Load Time Out untuk setiap attendance (check-out dengan type = 1)
        $attendances->getCollection()->transform(function ($attendance) {
            $timeOut = Attendance::where('emp_id', $attendance->emp_id)
                ->where('attendance_date', $attendance->attendance_date)
                ->where('type', 1) // Check-out
                ->first();

            $attendance->time_out = $timeOut ? $timeOut->attendance_time : null;
            return $attendance;
        });

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = round(($endTime - $startTime) * 1000, 2); // dalam milliseconds
        $memoryUsed = round(($endMemory - $startMemory) / 1024 / 1024, 2); // dalam MB

        // Log performance
        Log::info('Attendance Index Performance', [
            'execution_time_ms' => $executionTime,
            'execution_time_sec' => round($executionTime / 1000, 2),
            'memory_used_mb' => $memoryUsed,
            'total_records' => method_exists($attendances, 'total') ? $attendances->total() : 'N/A (simplePaginate)',
            'records_per_page' => $attendances->perPage(),
            'current_page' => $attendances->currentPage(),
            'pagination_type' => $useSimplePagination ? 'simplePaginate' : 'paginate',
            'has_filters' => $request->has('date_from') || $request->has('date_to')
        ]);

        return view('admin.attendance-employees.index')->with([
            'attendances' => $attendances,
            'performance' => [
                'execution_time_ms' => $executionTime,
                'execution_time_sec' => round($executionTime / 1000, 2),
                'memory_used_mb' => $memoryUsed
            ]
        ]);
    }

    public function indexLatetime(Request $request)
    {
        $query = Latetime::with('employee');

        // Filter berdasarkan tanggal
        if ($request->has('date_from') && $request->date_from) {
            $query->where('latetime_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('latetime_date', '<=', $request->date_to);
        }

        // Check if user is employee
        if (Auth::guard('employee')->check()) {
            $employee = Auth::guard('employee')->user();
            $query->where('emp_id', $employee->id_employees);
        }

        // Pagination: 50 records per page
        $latetimes = $query->orderBy('latetime_date', 'desc')
            ->paginate(50);

        return view('admin.attendance-employees.attendance-logs.latetime')->with([
            'latetimes' => $latetimes
        ]);
    }

    /**
     * Export attendance data to CSV
     * Only accessible by super_admin
     */
    public function export(Request $request)
    {
        // Check if user is super_admin
        $user = Auth::guard('employee')->check() ? Auth::guard('employee')->user() : Auth::guard('web')->user();
        if (!$user) {
            abort(403, 'Unauthorized access');
        }

        // Get user roles
        if (Auth::guard('employee')->check()) {
            $userRoles = $user->role ? [$user->role->slug] : ['employee'];
        } else {
            // Check if user has roles method (for User model)
            if (method_exists($user, 'roles') && $user instanceof \App\Models\User) {
                $userRoles = $user->roles()->pluck('slug')->toArray();
            } else {
                $userRoles = [];
            }
        }

        // Only super_admin can export
        if (!in_array('super_admin', $userRoles)) {
            abort(403, 'Only Super Admin can export attendance data');
        }

        // Build query same as index method
        $query = Attendance::select([
            'id_attendance',
            'emp_id',
            'attendance_time',
            'attendance_date',
            'status',
            'type'
        ])->where('type', 0); // Hanya check-in (type = 0)

        // Filter berdasarkan tanggal
        if ($request->has('date_from') && $request->date_from) {
            $query->where('attendance_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('attendance_date', '<=', $request->date_to);
        }

        // Filter berdasarkan employee (untuk admin)
        if ($request->has('emp_id') && $request->emp_id) {
            $query->where('emp_id', $request->emp_id);
        }

        // Check if user is employee
        if (Auth::guard('employee')->check()) {
            $employee = Auth::guard('employee')->user();
            $query->where('emp_id', $employee->id_employees);
        }

        // Eager load employee
        $query->with('employee:id_employees,name');

        // Get all records (no pagination for export)
        $attendances = $query->orderBy('attendance_date', 'desc')
            ->orderBy('attendance_time', 'desc')
            ->get();

        // Load Time Out untuk setiap attendance
        $attendances->transform(function ($attendance) {
            $timeOut = Attendance::where('emp_id', $attendance->emp_id)
                ->where('attendance_date', $attendance->attendance_date)
                ->where('type', 1) // Check-out
                ->first();

            $attendance->time_out = $timeOut ? $timeOut->attendance_time : null;
            return $attendance;
        });

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('HRSAKTI System')
            ->setTitle('Attendance Logs Export')
            ->setSubject('Attendance Data')
            ->setDescription('Export attendance logs data');

        // Set headers
        $headers = ['Date', 'Employee ID', 'Employee Name', 'Status', 'Time In', 'Time Out'];
        $sheet->fromArray($headers, null, 'A1');

        // Style header row
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);

        // Add data rows
        $row = 2;
        foreach ($attendances as $attendance) {
            $sheet->setCellValue('A' . $row, $attendance->attendance_date);
            $sheet->setCellValue('B' . $row, $attendance->emp_id);
            $sheet->setCellValue('C' . $row, $attendance->employee->name ?? 'N/A');
            $sheet->setCellValue('D' . $row, $attendance->status == 1 ? 'On Time' : 'Late');
            $sheet->setCellValue('E' . $row, $attendance->attendance_time ?? 'N/A');
            $sheet->setCellValue('F' . $row, $attendance->time_out ?? 'N/A');

            // Style data rows
            $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(false);
        }

        // Set header row height
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Generate filename
        $filename = 'attendance_logs_' . date('Y-m-d_His') . '.xlsx';

        // Create writer and save to temporary file
        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'attendance_export_');
        $writer->save($tempFile);

        // Return file download
        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Show the form for editing the specified attendance.
     * Only accessible by super_admin
     */
    public function edit($id)
    {
        try {
            // Check if user is super_admin
            $user = Auth::guard('employee')->check() ? Auth::guard('employee')->user() : Auth::guard('web')->user();
            if (!$user) {
                abort(403, 'Unauthorized access');
            }

            // Get user roles
            if (Auth::guard('employee')->check()) {
                $userRoles = $user->role ? [$user->role->slug] : ['employee'];
            } else {
                // Check if user has roles method (for User model)
                if (method_exists($user, 'roles') && $user instanceof \App\Models\User) {
                    $userRoles = $user->roles()->pluck('slug')->toArray();
                } else {
                    $userRoles = [];
                }
            }

            // Only super_admin can edit
            if (!in_array('super_admin', $userRoles)) {
                abort(403, 'Only Super Admin can edit attendance data');
            }

            // Find attendance record using the correct primary key
            $attendance = Attendance::with('employee')->where('id_attendance', $id)->first();

            if (!$attendance) {
                Log::warning('Attendance record not found', ['id' => $id]);
                abort(404, 'Attendance record not found');
            }

            // Validate required fields
            if (!$attendance->emp_id || !$attendance->attendance_date) {
                Log::error('Attendance record missing required fields', [
                    'id' => $id,
                    'emp_id' => $attendance->emp_id,
                    'attendance_date' => $attendance->attendance_date
                ]);
                return redirect()->route('attendance')
                    ->with('error', 'Attendance record is missing required information and cannot be edited.');
            }

            // Get time out if exists
            $timeOut = null;
            try {
                $timeOut = Attendance::where('emp_id', $attendance->emp_id)
                    ->where('attendance_date', $attendance->attendance_date)
                    ->where('type', 1) // Check-out
                    ->where('id_attendance', '!=', $attendance->id_attendance) // Exclude current record
                    ->first();
            } catch (\Exception $e) {
                Log::warning('Error fetching time out record', [
                    'emp_id' => $attendance->emp_id,
                    'attendance_date' => $attendance->attendance_date,
                    'error' => $e->getMessage()
                ]);
                // Continue without time out if there's an error
            }

            return view('admin.attendance-employees.edit', [
                'attendance' => $attendance,
                'timeOut' => $timeOut
            ]);
        } catch (\Exception $e) {
            Log::error('Error in AttendanceController@edit: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('attendance')
                ->with('error', 'An error occurred while loading the edit page: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified attendance in storage.
     * Only accessible by super_admin
     */
    public function update(Request $request, $id)
    {
        // Check if user is super_admin
        $user = Auth::guard('employee')->check() ? Auth::guard('employee')->user() : Auth::guard('web')->user();
        if (!$user) {
            abort(403, 'Unauthorized access');
        }

        // Get user roles
        if (Auth::guard('employee')->check()) {
            $userRoles = $user->role ? [$user->role->slug] : ['employee'];
        } else {
            // Check if user has roles method (for User model)
            if (method_exists($user, 'roles') && $user instanceof \App\Models\User) {
                $userRoles = $user->roles()->pluck('slug')->toArray();
            } else {
                $userRoles = [];
            }
        }

        // Only super_admin can update
        if (!in_array('super_admin', $userRoles)) {
            abort(403, 'Only Super Admin can update attendance data');
        }

        $request->validate([
            'attendance_date' => 'required|date',
            'attendance_time' => 'required|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:0,1'
        ]);

        DB::beginTransaction();
        try {
            $attendance = Attendance::findOrFail($id);

            // Format time to H:i:s
            $attendanceTime = $request->attendance_time;
            if (strlen($attendanceTime) == 5) { // H:i format
                $attendanceTime .= ':00'; // Add seconds
            }

            // Update check-in record
            $attendance->update([
                'attendance_date' => $request->attendance_date,
                'attendance_time' => $request->attendance_date . ' ' . $attendanceTime,
                'status' => $request->status
            ]);

            // Handle time out
            $timeOut = Attendance::where('emp_id', $attendance->emp_id)
                ->where('attendance_date', $request->attendance_date)
                ->where('type', 1) // Check-out
                ->first();

            if ($request->time_out) {
                // Format time out to H:i:s
                $timeOutTime = $request->time_out;
                if (strlen($timeOutTime) == 5) { // H:i format
                    $timeOutTime .= ':00'; // Add seconds
                }

                if ($timeOut) {
                    // Update existing time out
                    $timeOut->update([
                        'attendance_date' => $request->attendance_date,
                        'attendance_time' => $request->attendance_date . ' ' . $timeOutTime
                    ]);
                } else {
                    // Create new time out record
                    Attendance::create([
                        'emp_id' => $attendance->emp_id,
                        'attendance_date' => $request->attendance_date,
                        'attendance_time' => $request->attendance_date . ' ' . $timeOutTime,
                        'type' => 1, // Check-out
                        'status' => 1 // Default status for check-out
                    ]);
                }
            } else {
                // If time_out is empty, delete time out record if exists
                if ($timeOut) {
                    $timeOut->delete();
                }
            }

            DB::commit();

            return redirect()->route('attendance')
                ->with('success', 'Attendance record updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating attendance', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update attendance record: ' . $e->getMessage());
        }
    }
}
