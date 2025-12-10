<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Overtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function overtimeReport(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : Carbon::now()->endOfMonth();
        $employeeId = $request->get('employee_id');

        $query = Overtime::with('employee')
            ->whereBetween('overtime_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

        // Check if employee is logged in
        $isEmployee = false;
        $isSection = false;
        $currentEmployee = null;
        $canViewAll = false;

        if (Auth::guard('employee')->check()) {
            $isEmployee = true;
            $currentEmployee = Auth::guard('employee')->user();
            $position = $currentEmployee->position ?? '';

            // Section: can see reports from all employees in their section
            if (strpos($position, 'Section ') === 0) {
                $isSection = true;
                // Get all employees in this section (where id_section_employee = current section employee)
                $sectionEmployeeIds = Employee::where('id_section_employee', $currentEmployee->id_employees)
                    ->whereIn('position', ['Employees', 'Magang', 'PKL'])
                    ->pluck('id_employees')
                    ->toArray();

                // Filter overtimes from these employees
                $query->whereIn('emp_id', $sectionEmployeeIds);
                $canViewAll = true;
            } elseif (in_array($position, ['Wadir 1', 'Wadir 2', 'SDM/HRD'])) {
                // Wadir and SDM: can see all reports
                $canViewAll = true;
            } else {
                // Regular employees, Magang, PKL: only see their own reports
                $query->where('emp_id', $currentEmployee->id_employees);
                // Override employeeId filter if set
                if ($employeeId && $employeeId != $currentEmployee->id_employees) {
                    $employeeId = $currentEmployee->id_employees;
                }
            }
        } else {
            // Admin: can see all reports (if logged in as admin, they have access)
            $user = Auth::guard('web')->user();
            if ($user) {
                $canViewAll = true; // Admin users can view all reports
            }
        }

        // Apply employee filter if provided and user has permission
        if ($employeeId && $canViewAll) {
            $query->where('emp_id', $employeeId);
        }

        $overtimes = $query->orderBy('overtime_date', 'desc')->get();

        // Get employees list based on access
        if ($isSection) {
            // Section: only show employees in their section
            $employees = Employee::where('id_section_employee', $currentEmployee->id_employees)
                ->whereIn('position', ['Employees', 'Magang', 'PKL'])
                ->get();
        } elseif ($isEmployee && !$canViewAll) {
            // Regular employee: only show themselves
            $employees = collect([$currentEmployee]);
        } else {
            // Admin/Wadir/SDM: show all employees
            $employees = Employee::all();
        }

        // Calculate statistics
        $totalRecords = $overtimes->count();
        $totalEmployees = $overtimes->pluck('emp_id')->unique()->count();

        // Calculate total hours
        $totalHours = 0;
        foreach ($overtimes as $overtime) {
            if ($overtime->duration) {
                // Parse duration (format: HH:MM:SS or HH:MM)
                $durationParts = explode(':', $overtime->duration);
                if (count($durationParts) >= 2) {
                    $hours = (int)$durationParts[0];
                    $minutes = (int)$durationParts[1];
                    $totalHours += $hours + ($minutes / 60);
                }
            }
        }

        // Average duration
        $averageHours = $totalRecords > 0 ? round($totalHours / $totalRecords, 2) : 0;

        // Status breakdown
        $statusBreakdown = $overtimes->groupBy('status')->map->count();
        $pendingCount = $statusBreakdown->get('pending', 0);
        $approvedCount = $statusBreakdown->get('approved', 0);
        $rejectedCount = $statusBreakdown->get('rejected', 0);

        // Approval status breakdown
        $sectionApprovedCount = $overtimes->where('section_approved', true)->count();
        $wadirApprovedCount = $overtimes->where('wadir_approved', true)->count();
        $sdmApprovedCount = $overtimes->where('sdm_approved', true)->count();

        // Top employees by overtime count
        $topEmployees = $overtimes->groupBy('emp_id')->map(function ($group) {
            $totalHours = 0;
            foreach ($group as $overtime) {
                if ($overtime->duration) {
                    $durationParts = explode(':', $overtime->duration);
                    if (count($durationParts) >= 2) {
                        $hours = (int)$durationParts[0];
                        $minutes = (int)$durationParts[1];
                        $totalHours += $hours + ($minutes / 60);
                    }
                }
            }
            return [
                'employee' => $group->first()->employee,
                'count' => $group->count(),
                'total_hours' => round($totalHours, 2)
            ];
        })->sortByDesc('count')->take(5);

        return view('admin.overtime.reports.reports', compact(
            'overtimes',
            'employees',
            'startDate',
            'endDate',
            'employeeId',
            'totalRecords',
            'totalEmployees',
            'totalHours',
            'averageHours',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'sectionApprovedCount',
            'wadirApprovedCount',
            'sdmApprovedCount',
            'topEmployees',
            'isEmployee',
            'isSection',
            'canViewAll',
            'currentEmployee'
        ));
    }

    public function exportOvertimeReport(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : Carbon::now()->endOfMonth();
        $employeeId = $request->get('employee_id');

        $query = Overtime::with('employee')
            ->whereBetween('overtime_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

        // Check if employee is logged in
        $isEmployee = false;
        $isSection = false;
        $currentEmployee = null;
        $canViewAll = false;

        if (Auth::guard('employee')->check()) {
            $isEmployee = true;
            $currentEmployee = Auth::guard('employee')->user();
            $position = $currentEmployee->position ?? '';

            // Section: can see reports from all employees in their section
            if (strpos($position, 'Section ') === 0) {
                $isSection = true;
                // Get all employees in this section (where id_section_employee = current section employee)
                $sectionEmployeeIds = Employee::where('id_section_employee', $currentEmployee->id_employees)
                    ->whereIn('position', ['Employees', 'Magang', 'PKL'])
                    ->pluck('id_employees')
                    ->toArray();

                // Filter overtimes from these employees
                $query->whereIn('emp_id', $sectionEmployeeIds);
                $canViewAll = true;
            } elseif (in_array($position, ['Wadir 1', 'Wadir 2', 'SDM/HRD'])) {
                // Wadir and SDM: can see all reports
                $canViewAll = true;
            } else {
                // Regular employees, Magang, PKL: only see their own reports
                $query->where('emp_id', $currentEmployee->id_employees);
                // Override employeeId filter if set
                if ($employeeId && $employeeId != $currentEmployee->id_employees) {
                    $employeeId = $currentEmployee->id_employees;
                }
            }
        } else {
            // Admin: can see all reports
            $user = Auth::guard('web')->user();
            if ($user) {
                $canViewAll = true; // Admin users can view all reports
            }
        }

        // Apply employee filter if provided and user has permission
        if ($employeeId && $canViewAll) {
            $query->where('emp_id', $employeeId);
        }

        $overtimes = $query->get();

        $filename = 'overtime_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($overtimes) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Employee ID',
                'Employee Name',
                'Overtime Date',
                'Start Time',
                'End Time',
                'Duration',
                'Created At'
            ]);

            // Data rows
            foreach ($overtimes as $overtime) {
                fputcsv($file, [
                    $overtime->emp_id,
                    $overtime->employee->name ?? 'N/A',
                    $overtime->overtime_date,
                    $overtime->start_time ? \Carbon\Carbon::parse($overtime->start_time)->format('H:i:s') : 'N/A',
                    $overtime->end_time ? \Carbon\Carbon::parse($overtime->end_time)->format('H:i:s') : 'N/A',
                    $overtime->duration ?? 'N/A',
                    $overtime->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
