<?php

namespace App\Http\Controllers;

use App\Models\Latetime;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

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
}
