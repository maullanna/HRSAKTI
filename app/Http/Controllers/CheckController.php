<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Leave;
use Illuminate\Support\Facades\Auth;

class CheckController extends Controller
{
    public function index()
    {
        // Check if user is employee
        if (Auth::guard('employee')->check()) {
            $employee = Auth::guard('employee')->user();
            // Employee can only see their own data
            $employees = Employee::where('id_employees', $employee->id_employees)->get();
        } else {
            // Admin can see all employees
            $employees = Employee::all();
        }

        return view('admin.master-data.employees.check')->with(['employees' => $employees]);
    }

    public function CheckStore(Request $request)
    {
        // Check if user is employee
        if (Auth::guard('employee')->check()) {
            $loggedInEmployee = Auth::guard('employee')->user();
        }

        if (isset($request->attd)) {
            foreach ($request->attd as $keys => $values) {
                foreach ($values as $key => $value) {
                    // Find employee by id_employees (primary key)
                    $employee = Employee::where('id_employees', $key)->first();

                    // If employee is logged in, only allow them to update their own attendance
                    if (Auth::guard('employee')->check()) {
                        if ($key != $loggedInEmployee->id_employees) {
                            continue; // Skip if trying to update other employee's attendance
                        }
                    }

                    if ($employee) {
                        if (
                            !Attendance::whereAttendance_date($keys)
                                ->whereEmp_id($employee->id_employees)
                                ->whereType(0)
                                ->first()
                        ) {
                            $data = new Attendance();

                            $data->emp_id = $employee->id_employees;
                            $data->attendance_time = date('H:i:s');
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
        // Check if user is employee
        if (Auth::guard('employee')->check()) {
            $employee = Auth::guard('employee')->user();
            // Employee can only see their own data
            $employees = Employee::where('id_employees', $employee->id_employees)->get();
        } else {
            // Admin can see all employees
            $employees = Employee::all();
        }

        return view('admin.attendance-employees.attendance-sheet.index')->with(['employees' => $employees]);
    }
}
