<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Latetime;
use App\Models\Attendance;
use App\Models\Training;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class AdminController extends Controller
{


    public function index()
    {
        // Check if employee is logged in
        if (Auth::guard('employee')->check()) {
            $employee = Auth::guard('employee')->user();

            // Log for debugging
            Log::info('Employee dashboard accessed', [
                'employee_id' => $employee->id_employees,
                'employee_name' => $employee->name,
                'employee_code' => $employee->employee_code ?? 'N/A'
            ]);

            // Get statistics for the current month
            $currentMonth = now()->startOfMonth();
            $currentMonthEnd = now()->endOfMonth();

            // Attendance statistics - Only count check-in records (type = 0)
            $totalAttendance = Attendance::where('emp_id', $employee->id_employees)
                ->where('type', 0) // Only check-in records
                ->whereBetween('attendance_date', [$currentMonth->format('Y-m-d'), $currentMonthEnd->format('Y-m-d')])
                ->count();

            // Log attendance count for debugging
            Log::info('Employee attendance statistics', [
                'employee_id' => $employee->id_employees,
                'total_attendance' => $totalAttendance,
                'month' => $currentMonth->format('Y-m-d') . ' to ' . $currentMonthEnd->format('Y-m-d')
            ]);

            $onTimeAttendance = Attendance::where('emp_id', $employee->id_employees)
                ->where('type', 0) // Only check-in records
                ->whereBetween('attendance_date', [$currentMonth->format('Y-m-d'), $currentMonthEnd->format('Y-m-d')])
                ->where('status', 1)
                ->count();

            $lateAttendance = Attendance::where('emp_id', $employee->id_employees)
                ->where('type', 0) // Only check-in records
                ->whereBetween('attendance_date', [$currentMonth->format('Y-m-d'), $currentMonthEnd->format('Y-m-d')])
                ->where('status', 0)
                ->count();

            // Calculate On Time Percentage
            $onTimePercentage = 0;
            if ($totalAttendance > 0) {
                $onTimePercentage = round(($onTimeAttendance / $totalAttendance) * 100, 1);
            }

            // Today's attendance statistics
            $onTimeToday = Attendance::where('emp_id', $employee->id_employees)
                ->where('type', 0) // Only check-in records
                ->where('attendance_date', now()->format('Y-m-d'))
                ->where('status', 1)
                ->count();

            $lateToday = Attendance::where('emp_id', $employee->id_employees)
                ->where('type', 0) // Only check-in records
                ->where('attendance_date', now()->format('Y-m-d'))
                ->where('status', 0)
                ->count();

            // Today's attendance (check-in record)
            $todayAttendance = Attendance::where('emp_id', $employee->id_employees)
                ->where('type', 0) // Only check-in records
                ->where('attendance_date', now()->format('Y-m-d'))
                ->first();

            // Leave statistics
            $totalLeaves = \App\Models\Leave::where('emp_id', $employee->id_employees)
                ->whereBetween('leave_date', [$currentMonth->format('Y-m-d'), $currentMonthEnd->format('Y-m-d')])
                ->count();

            $approvedLeaves = \App\Models\Leave::where('emp_id', $employee->id_employees)
                ->whereBetween('leave_date', [$currentMonth->format('Y-m-d'), $currentMonthEnd->format('Y-m-d')])
                ->where('status', 'approved')
                ->count();

            $pendingLeaves = \App\Models\Leave::where('emp_id', $employee->id_employees)
                ->where('status', 'pending')
                ->count();

            // Overtime statistics
            $totalOvertimes = \App\Models\Overtime::where('emp_id', $employee->id_employees)
                ->whereBetween('overtime_date', [$currentMonth->format('Y-m-d'), $currentMonthEnd->format('Y-m-d')])
                ->count();

            $approvedOvertimes = \App\Models\Overtime::where('emp_id', $employee->id_employees)
                ->whereBetween('overtime_date', [$currentMonth->format('Y-m-d'), $currentMonthEnd->format('Y-m-d')])
                ->where('status', 'approved')
                ->count();

            $pendingOvertimes = \App\Models\Overtime::where('emp_id', $employee->id_employees)
                ->where('status', 'pending')
                ->count();

            // Recent activities - Only check-in records
            $recentAttendances = Attendance::where('emp_id', $employee->id_employees)
                ->where('type', 0) // Only check-in records
                ->orderBy('attendance_date', 'desc')
                ->orderBy('attendance_time', 'desc')
                ->limit(5)
                ->get();

            $recentLeaves = \App\Models\Leave::where('emp_id', $employee->id_employees)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $recentOvertimes = \App\Models\Overtime::where('emp_id', $employee->id_employees)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Training statistics for employee
            $employeeTrainings = Training::where('employee_id', $employee->id_employees)
                ->orderBy('start_date', 'desc')
                ->get();

            $employeeTrainingsThisMonth = Training::where('employee_id', $employee->id_employees)
                ->whereYear('start_date', now()->year)
                ->whereMonth('start_date', now()->month)
                ->with('employee')
                ->get();

            $employeeTrainingMonthlyData = [];
            $employeeTrainingMonthlyLabels = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $monthLabel = $date->format('M Y');
                $monthStart = $date->copy()->startOfMonth()->format('Y-m-d');
                $monthEnd = $date->copy()->endOfMonth()->format('Y-m-d');

                $trainingCount = Training::where('employee_id', $employee->id_employees)
                    ->whereBetween('start_date', [$monthStart, $monthEnd])
                    ->count();

                $employeeTrainingMonthlyLabels[] = $monthLabel;
                $employeeTrainingMonthlyData[] = $trainingCount;
            }

            // Training statistics
            $totalEmployeeTrainings = Training::where('employee_id', $employee->id_employees)->count();
            $completedEmployeeTrainings = Training::where('employee_id', $employee->id_employees)
                ->where('status', 'completed')
                ->count();
            $ongoingEmployeeTrainings = Training::where('employee_id', $employee->id_employees)
                ->where('status', 'ongoing')
                ->count();

            // Approval statistics for Section, Wadir, SDM/HRD
            $pendingLeaveApprovals = 0;
            $pendingOvertimeApprovals = 0;
            $position = $employee->position ?? '';

            // Log position for debugging
            Log::info('Employee position check', [
                'employee_id' => $employee->id_employees,
                'employee_name' => $employee->name,
                'position' => $position,
                'is_regular' => !(strpos($position, 'Section ') === 0 || in_array($position, ['Wadir 1', 'Wadir 2']) || $position === 'SDM/HRD')
            ]);

            // Check if employee has approval role
            // Note: Using status field only as approved_by columns may not exist
            if (strpos($position, 'Section ') === 0) {
                // Section: count pending leaves and overtime that need section approval
                $pendingLeaveApprovals = \App\Models\Leave::where('status', 'pending')
                    ->count();

                $pendingOvertimeApprovals = \App\Models\Overtime::where('status', 'pending')
                    ->where('id_section_employee', $employee->id_employees)
                    ->where('section_approved', false)
                    ->count();
            } elseif (in_array($position, ['Wadir 1', 'Wadir 2'])) {
                // Wadir: count pending leaves and overtime that need wadir approval
                // Leaves with status 'approved_section' need wadir approval
                $pendingLeaveApprovals = \App\Models\Leave::where('status', 'approved_section')
                    ->count();

                $pendingOvertimeApprovals = \App\Models\Overtime::where('status', 'pending')
                    ->where('id_wadir_employee', $employee->id_employees)
                    ->where('section_approved', true)
                    ->where('wadir_approved', false)
                    ->count();
            } elseif ($position === 'SDM/HRD') {
                // SDM/HRD: count pending leaves and overtime that need SDM approval
                // Leaves with status 'approved_wadir' need SDM/admin approval
                $pendingLeaveApprovals = \App\Models\Leave::where('status', 'approved_wadir')
                    ->count();

                $pendingOvertimeApprovals = \App\Models\Overtime::where('status', 'pending')
                    ->where('id_sdm_employee', $employee->id_employees)
                    ->where('section_approved', true)
                    ->where('wadir_approved', true)
                    ->where('sdm_approved', false)
                    ->count();
            }

            // Ensure all variables are set with defaults
            $data = [
                'employee' => $employee,
                'totalAttendance' => $totalAttendance ?? 0,
                'onTimeAttendance' => $onTimeAttendance ?? 0,
                'lateAttendance' => $lateAttendance ?? 0,
                'onTimePercentage' => $onTimePercentage ?? 0,
                'onTimeToday' => $onTimeToday ?? 0,
                'lateToday' => $lateToday ?? 0,
                'todayAttendance' => $todayAttendance ?? null,
                'totalLeaves' => $totalLeaves ?? 0,
                'approvedLeaves' => $approvedLeaves ?? 0,
                'pendingLeaves' => $pendingLeaves ?? 0,
                'totalOvertimes' => $totalOvertimes ?? 0,
                'approvedOvertimes' => $approvedOvertimes ?? 0,
                'pendingOvertimes' => $pendingOvertimes ?? 0,
                'recentAttendances' => $recentAttendances ?? collect(),
                'recentLeaves' => $recentLeaves ?? collect(),
                'recentOvertimes' => $recentOvertimes ?? collect(),
                'employeeTrainings' => $employeeTrainings ?? collect(),
                'employeeTrainingsThisMonth' => $employeeTrainingsThisMonth ?? collect(),
                'employeeTrainingMonthlyLabels' => $employeeTrainingMonthlyLabels ?? [],
                'employeeTrainingMonthlyData' => $employeeTrainingMonthlyData ?? [],
                'totalEmployeeTrainings' => $totalEmployeeTrainings ?? 0,
                'completedEmployeeTrainings' => $completedEmployeeTrainings ?? 0,
                'ongoingEmployeeTrainings' => $ongoingEmployeeTrainings ?? 0,
                'pendingLeaveApprovals' => $pendingLeaveApprovals ?? 0,
                'pendingOvertimeApprovals' => $pendingOvertimeApprovals ?? 0,
                'position' => $position ?? ''
            ];

            // Log before returning view
            Log::info('Returning employee dashboard view', [
                'employee_id' => $employee->id_employees,
                'view_data_keys' => array_keys($data),
                'total_attendance' => $data['totalAttendance'],
                'position' => $data['position']
            ]);

            try {
                return view('employee.dashboard', $data);
            } catch (\Exception $e) {
                Log::error('Error rendering employee dashboard', [
                    'employee_id' => $employee->id_employees,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        }

        //Dashboard statistics for admin
        $totalEmp = count(Employee::all());
        // Only count check-in records (type = 0) for today's statistics
        $AllAttendance = Attendance::where('attendance_date', date("Y-m-d"))
            ->where('type', 0) // Only check-in records
            ->count();
        $ontimeEmp = Attendance::where('attendance_date', date("Y-m-d"))
            ->where('type', 0) // Only check-in records
            ->where('status', 1)
            ->count();
        $latetimeEmp = Attendance::where('attendance_date', date("Y-m-d"))
            ->where('type', 0) // Only check-in records
            ->where('status', 0)
            ->count();

        if ($AllAttendance > 0) {
            $percentageOntime = round(($ontimeEmp / $AllAttendance) * 100, 1);
        } else {
            $percentageOntime = 0;
        }

        // Get monthly attendance data for chart (last 8 weeks or days)
        $monthlyData = [];
        $monthlyLabels = [];

        // Get attendance data for last 8 days - Only check-in records
        for ($i = 7; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayLabel = now()->subDays($i)->format('M d');
            $dayAttendance = Attendance::where('attendance_date', $date)
                ->where('type', 0) // Only check-in records
                ->count();

            $monthlyLabels[] = $dayLabel;
            $monthlyData[] = $dayAttendance;
        }

        // Get current month statistics - Only check-in records
        $currentMonth = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();
        $thisMonthAttendance = Attendance::where('type', 0) // Only check-in records
            ->whereBetween('attendance_date', [
                $currentMonth->format('Y-m-d'),
                $currentMonthEnd->format('Y-m-d')
            ])->count();

        // Get last month statistics - Only check-in records
        $lastMonth = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();
        $lastMonthAttendance = Attendance::where('type', 0) // Only check-in records
            ->whereBetween('attendance_date', [
                $lastMonth->format('Y-m-d'),
                $lastMonthEnd->format('Y-m-d')
            ])->count();

        // Get attendance statistics for analytics - Only check-in records
        $totalAttendanceThisMonth = Attendance::where('type', 0) // Only check-in records
            ->whereBetween('attendance_date', [
                $currentMonth->format('Y-m-d'),
                $currentMonthEnd->format('Y-m-d')
            ])->count();

        $onTimeThisMonth = Attendance::where('type', 0) // Only check-in records
            ->whereBetween('attendance_date', [
                $currentMonth->format('Y-m-d'),
                $currentMonthEnd->format('Y-m-d')
            ])->where('status', 1)->count();

        $lateThisMonth = Attendance::where('type', 0) // Only check-in records
            ->whereBetween('attendance_date', [
                $currentMonth->format('Y-m-d'),
                $currentMonthEnd->format('Y-m-d')
            ])->where('status', 0)->count();

        // Get on-time and late data for last 8 days (for peity charts) - Only check-in records
        $onTimeData = [];
        $lateData = [];
        for ($i = 7; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $onTimeData[] = Attendance::where('attendance_date', $date)
                ->where('type', 0) // Only check-in records
                ->where('status', 1)
                ->count();
            $lateData[] = Attendance::where('attendance_date', $date)
                ->where('type', 0) // Only check-in records
                ->where('status', 0)
                ->count();
        }

        $data = [$totalEmp, $ontimeEmp, $latetimeEmp, $percentageOntime];

        // Training statistics
        $currentYear = now()->year;
        $trainingsThisMonth = Training::whereYear('start_date', $currentYear)
            ->whereMonth('start_date', now()->month)
            ->with('employee')
            ->orderBy('start_date', 'desc')
            ->get();

        // Training data per month for chart (last 12 months)
        $trainingMonthlyData = [];
        $trainingMonthlyLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthLabel = $date->format('M Y');
            $monthStart = $date->copy()->startOfMonth()->format('Y-m-d');
            $monthEnd = $date->copy()->endOfMonth()->format('Y-m-d');

            $trainingCount = Training::whereBetween('start_date', [$monthStart, $monthEnd])->count();

            $trainingMonthlyLabels[] = $monthLabel;
            $trainingMonthlyData[] = $trainingCount;
        }

        // Training statistics
        $totalTrainings = Training::count();
        $completedTrainings = Training::where('status', 'completed')->count();
        $ongoingTrainings = Training::where('status', 'ongoing')->count();
        $plannedTrainings = Training::where('status', 'planned')->count();

        // Training by category
        $trainingByCategory = Training::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get()
            ->pluck('count', 'category')
            ->toArray();

        return view('admin.dashboard.index', compact(
            'data',
            'monthlyLabels',
            'monthlyData',
            'thisMonthAttendance',
            'lastMonthAttendance',
            'totalAttendanceThisMonth',
            'onTimeThisMonth',
            'lateThisMonth',
            'onTimeData',
            'lateData',
            'trainingsThisMonth',
            'trainingMonthlyLabels',
            'trainingMonthlyData',
            'totalTrainings',
            'completedTrainings',
            'ongoingTrainings',
            'plannedTrainings',
            'trainingByCategory'
        ));
    }
}
