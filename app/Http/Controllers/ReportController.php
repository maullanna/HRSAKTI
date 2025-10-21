<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Overtime;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function overtimeReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        $employeeId = $request->get('employee_id');

        $query = Overtime::with('employee')
            ->whereBetween('overtime_date', [$startDate, $endDate]);

        if ($employeeId) {
            $query->where('emp_id', $employeeId);
        }

        $overtimes = $query->get();
        $employees = Employee::all();

        return view('admin.overtime.reports', compact('overtimes', 'employees', 'startDate', 'endDate', 'employeeId'));
    }

    public function exportOvertimeReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        $employeeId = $request->get('employee_id');

        $query = Overtime::with('employee')
            ->whereBetween('overtime_date', [$startDate, $endDate]);

        if ($employeeId) {
            $query->where('emp_id', $employeeId);
        }

        $overtimes = $query->get();

        $filename = 'overtime_report_' . $startDate . '_to_' . $endDate . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($overtimes) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Employee ID',
                'Employee Name',
                'Overtime Date',
                'Duration',
                'Created At'
            ]);

            // Data rows
            foreach ($overtimes as $overtime) {
                fputcsv($file, [
                    $overtime->employee->id,
                    $overtime->employee->name,
                    $overtime->overtime_date,
                    $overtime->duration,
                    $overtime->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
