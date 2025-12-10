<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Latetime;
use App\Models\Attendance;
use App\Models\Training;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class AdminController extends Controller
{


    public function index()
    {
        // Check if employee is logged in
        if (Auth::guard('employee')->check()) {
            $employee = Auth::guard('employee')->user();

            // Get statistics for the current month
            $currentMonth = now()->startOfMonth();
            $currentMonthEnd = now()->endOfMonth();

            // Attendance statistics
            $totalAttendance = Attendance::where('emp_id', $employee->id_employees)
                ->whereBetween('attendance_date', [$currentMonth->format('Y-m-d'), $currentMonthEnd->format('Y-m-d')])
                ->count();

            $onTimeAttendance = Attendance::where('emp_id', $employee->id_employees)
                ->whereBetween('attendance_date', [$currentMonth->format('Y-m-d'), $currentMonthEnd->format('Y-m-d')])
                ->where('status', 1)
                ->count();

            $lateAttendance = Attendance::where('emp_id', $employee->id_employees)
                ->whereBetween('attendance_date', [$currentMonth->format('Y-m-d'), $currentMonthEnd->format('Y-m-d')])
                ->where('status', 0)
                ->count();

            // Today's attendance
            $todayAttendance = Attendance::where('emp_id', $employee->id_employees)
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

            // Recent activities
            $recentAttendances = Attendance::where('emp_id', $employee->id_employees)
                ->orderBy('attendance_date', 'desc')
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

            return view('employee.dashboard', compact(
                'employee',
                'totalAttendance',
                'onTimeAttendance',
                'lateAttendance',
                'todayAttendance',
                'totalLeaves',
                'approvedLeaves',
                'pendingLeaves',
                'totalOvertimes',
                'approvedOvertimes',
                'pendingOvertimes',
                'recentAttendances',
                'recentLeaves',
                'recentOvertimes',
                'employeeTrainings',
                'employeeTrainingsThisMonth',
                'employeeTrainingMonthlyLabels',
                'employeeTrainingMonthlyData',
                'totalEmployeeTrainings',
                'completedEmployeeTrainings',
                'ongoingEmployeeTrainings',
                'pendingLeaveApprovals',
                'pendingOvertimeApprovals',
                'position'
            ));
        }

        //Dashboard statistics for admin
        $totalEmp = count(Employee::all());
        $AllAttendance = count(Attendance::whereAttendance_date(date("Y-m-d"))->get());
        $ontimeEmp = count(Attendance::whereAttendance_date(date("Y-m-d"))->whereStatus('1')->get());
        $latetimeEmp = count(Attendance::whereAttendance_date(date("Y-m-d"))->whereStatus('0')->get());

        if ($AllAttendance > 0) {
            $percentageOntime = round(($ontimeEmp / $AllAttendance) * 100, 1);
        } else {
            $percentageOntime = 0;
        }

        // Get monthly attendance data for chart (last 8 weeks or days)
        $monthlyData = [];
        $monthlyLabels = [];

        // Get attendance data for last 8 days
        for ($i = 7; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayLabel = now()->subDays($i)->format('M d');
            $dayAttendance = Attendance::where('attendance_date', $date)->count();

            $monthlyLabels[] = $dayLabel;
            $monthlyData[] = $dayAttendance;
        }

        // Get current month statistics
        $currentMonth = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();
        $thisMonthAttendance = Attendance::whereBetween('attendance_date', [
            $currentMonth->format('Y-m-d'),
            $currentMonthEnd->format('Y-m-d')
        ])->count();

        // Get last month statistics
        $lastMonth = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();
        $lastMonthAttendance = Attendance::whereBetween('attendance_date', [
            $lastMonth->format('Y-m-d'),
            $lastMonthEnd->format('Y-m-d')
        ])->count();

        // Get attendance statistics for analytics
        $totalAttendanceThisMonth = Attendance::whereBetween('attendance_date', [
            $currentMonth->format('Y-m-d'),
            $currentMonthEnd->format('Y-m-d')
        ])->count();

        $onTimeThisMonth = Attendance::whereBetween('attendance_date', [
            $currentMonth->format('Y-m-d'),
            $currentMonthEnd->format('Y-m-d')
        ])->where('status', 1)->count();

        $lateThisMonth = Attendance::whereBetween('attendance_date', [
            $currentMonth->format('Y-m-d'),
            $currentMonthEnd->format('Y-m-d')
        ])->where('status', 0)->count();

        // Get on-time and late data for last 8 days (for peity charts)
        $onTimeData = [];
        $lateData = [];
        for ($i = 7; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $onTimeData[] = Attendance::where('attendance_date', $date)->where('status', 1)->count();
            $lateData[] = Attendance::where('attendance_date', $date)->where('status', 0)->count();
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
