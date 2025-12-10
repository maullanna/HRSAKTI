<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\User;
use App\Models\Employee;
use App\Models\Overtime;
use App\Models\FingerDevices;
use App\Helpers\FingerHelper;
use App\Models\Leave;
use App\Http\Requests\AttendanceEmp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index()
    {
        $leaves = Leave::with('employee')->latest()->get();
        return view('admin.leaves.index', compact('leaves'));
    }

    public function create()
    {
        $employees = Employee::all();

        // Get current employee if logged in as employee
        $currentEmployee = null;
        if (Auth::guard('employee')->check()) {
            $currentEmployee = Auth::guard('employee')->user();
        }

        return view('admin.leaves.create', compact('employees', 'currentEmployee'));
    }

    public function store(Request $request)
    {
        // If employee is logged in, auto-set emp_id and status
        $isEmployee = Auth::guard('employee')->check();
        $empId = $isEmployee ? Auth::guard('employee')->user()->id_employees : $request->emp_id;
        $status = $isEmployee ? 'pending' : ($request->status ?? 'pending');

        $request->validate([
            'emp_id' => $isEmployee ? 'nullable' : 'required|exists:employees,id_employees',
            'leave_date' => 'required|date|after_or_equal:today',
            'leave_time' => 'required|date',
            'type' => 'required|in:sick,vacation,personal,emergency,maternity,paternity,study,other',
            'state' => 'nullable|string|max:255',
            'status' => $isEmployee ? 'nullable' : 'required|in:pending,approved,rejected,cancelled',
        ]);

        $leave = new Leave();
        $leave->emp_id = $empId;
        $leave->leave_date = $request->leave_date;
        $leave->leave_time = $request->leave_time;
        $leave->type = $request->type;
        $leave->state = $request->state;
        $leave->status = $status;

        // Set uid - use employee id if employee, or user id if admin
        if ($isEmployee) {
            // Employee model uses email as auth identifier, so we need to get id_employees directly
            $leave->uid = Auth::guard('employee')->user()->id_employees ?? 0;
        } else {
            $leave->uid = Auth::id() ?? 0; // Default to 0 if no user
        }

        $leave->save();

        return redirect()->route('leave.index')->with('success', 'Leave request created successfully.');
    }

    public function show($leave)
    {
        // Find leave by id_leave since route parameter is named 'leave'
        $leave = Leave::where('id_leave', $leave)->firstOrFail();
        $leave->load('employee');
        return view('admin.leaves.show', compact('leave'));
    }

    public function edit($leave)
    {
        // Find leave by id_leave since route parameter is named 'leave'
        $leave = Leave::where('id_leave', $leave)->firstOrFail();
        $employees = Employee::all();

        // Get current employee if logged in as employee
        $currentEmployee = null;
        if (Auth::guard('employee')->check()) {
            $currentEmployee = Auth::guard('employee')->user();
            // Employee can only edit their own leave requests
            if ($leave->emp_id != $currentEmployee->id_employees) {
                return redirect()->route('leave.index')->with('error', 'You can only edit your own leave requests.');
            }
        }

        return view('admin.leaves.edit', compact('leave', 'employees', 'currentEmployee'));
    }

    public function update(Request $request, $leave)
    {
        // Find leave by id_leave since route parameter is named 'leave'
        $leave = Leave::where('id_leave', $leave)->firstOrFail();

        // If employee is logged in, auto-set emp_id and prevent status change
        $isEmployee = Auth::guard('employee')->check();
        $empId = $isEmployee ? Auth::guard('employee')->user()->id_employees : $request->emp_id;

        // Employee can only edit their own leave
        if ($isEmployee && $leave->emp_id != $empId) {
            return redirect()->route('leave.index')->with('error', 'You can only edit your own leave requests.');
        }

        $request->validate([
            'emp_id' => $isEmployee ? 'nullable' : 'required|exists:employees,id_employees',
            'leave_date' => 'required|date',
            'leave_time' => 'required|date',
            'type' => 'required|in:sick,vacation,personal,emergency,maternity,paternity,study,other',
            'state' => 'nullable|string|max:255',
            'status' => $isEmployee ? 'nullable' : 'required|in:pending,approved,rejected,cancelled',
        ]);

        $leave->emp_id = $empId;
        $leave->leave_date = $request->leave_date;
        $leave->leave_time = $request->leave_time;
        $leave->type = $request->type;
        $leave->state = $request->state;

        // Only admin can change status
        if (!$isEmployee) {
            $leave->status = $request->status;
        }
        // Employee cannot change status, it stays as is

        $leave->save();

        return redirect()->route('leave.index')->with('success', 'Leave request updated successfully.');
    }

    public function destroy(Leave $leave)
    {
        $leave->delete();
        return redirect()->route('leave.index')->with('success', 'Leave request deleted successfully.');
    }

    public function approve(Leave $leave)
    {
        $leave->status = 'approved';
        $leave->save();

        return redirect()->route('leave.index')->with('success', 'Leave request approved successfully.');
    }

    public function reject(Leave $leave)
    {
        $leave->status = 'rejected';
        $leave->save();

        return redirect()->route('leave.index')->with('success', 'Leave request rejected successfully.');
    }

    public function indexOvertime()
    {
        return view('admin.overtime.index')->with(['overtimes' => Overtime::all()]);
    }

    // Schedule functionality removed - overtime calculation disabled
    // public static function overTimeDevice($att_dateTime, Employee $employee)
    // {
    //     $attendance_time = new DateTime($att_dateTime);
    //     $checkout = new DateTime($employee->schedules->first()->time_out);
    //     $difference = $checkout->diff($attendance_time)->format('%H:%I:%S');
    //
    //     $overtime = new Overtime();
    //     $overtime->emp_id = $employee->id;
    //     $overtime->duration = $difference;
    //     $overtime->overtime_date = date('Y-m-d', strtotime($att_dateTime));
    //     $overtime->save();
    // }
}
