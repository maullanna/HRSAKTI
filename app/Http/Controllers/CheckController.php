<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Leave;

class CheckController extends Controller
{
    public function index()
    {
        return view('admin.master-data.employees.check')->with(['employees' => Employee::all()]);
    }

    public function CheckStore(Request $request)
    {
        if (isset($request->attd)) {
            foreach ($request->attd as $keys => $values) {
                foreach ($values as $key => $value) {
                    // Find employee by id_employees
                    $employee = Employee::where('id_employees', $key)->first();
                    
                    if ($employee) {
                        if (
                            !Attendance::whereAttendance_date($keys)
                                ->whereEmp_id($employee->id_employees)
                                ->whereType(0)
                                ->first()
                        ) {
                            $data = new Attendance();
                            
                            $data->emp_id = $employee->id_employees;
                            $schedule = $employee->schedules->first();
                            $data->attendance_time = $schedule ? date('H:i:s', strtotime($schedule->time_in)) : date('H:i:s');
                            $data->attendance_date = $keys;
                            $data->type = 0;
                            $data->status = 1; // Default on-time, can be updated later
                            
                            $data->save();
                        }
                    }
                }
            }
        }
        // Leave checkbox removed - use Leave menu instead
        // Flash message will be handled by redirect
        return back();
    }
    public function sheetReport()
    {

    return view('admin.attendance-employees.attendance-sheet.index')->with(['employees' => Employee::all()]);
    }
}
