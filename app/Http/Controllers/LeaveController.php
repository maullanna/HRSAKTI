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
        return view('admin.leaves.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'emp_id' => 'required|exists:employees,id',
            'leave_date' => 'required|date|after_or_equal:today',
            'leave_time' => 'required|date',
            'type' => 'required|in:sick,vacation,personal,emergency,maternity,paternity,study,other',
            'state' => 'nullable|string|max:255',
            'status' => 'required|in:pending,approved,rejected,cancelled',
        ]);

        $leave = new Leave();
        $leave->emp_id = $request->emp_id;
        $leave->leave_date = $request->leave_date;
        $leave->leave_time = $request->leave_time;
        $leave->type = $request->type;
        $leave->state = $request->state;
        $leave->status = $request->status;
        $leave->uid = Auth::id(); // Set the user who created the leave
        $leave->save();

        return redirect()->route('leave.index')->with('success', 'Leave request created successfully.');
    }

    public function show(Leave $leave)
    {
        $leave->load('employee');
        return view('admin.leaves.show', compact('leave'));
    }

    public function edit(Leave $leave)
    {
        $employees = Employee::all();
        return view('admin.leaves.edit', compact('leave', 'employees'));
    }

    public function update(Request $request, Leave $leave)
    {
        $request->validate([
            'emp_id' => 'required|exists:employees,id',
            'leave_date' => 'required|date',
            'leave_time' => 'required|date',
            'type' => 'required|in:sick,vacation,personal,emergency,maternity,paternity,study,other',
            'state' => 'nullable|string|max:255',
            'status' => 'required|in:pending,approved,rejected,cancelled',
        ]);

        $leave->emp_id = $request->emp_id;
        $leave->leave_date = $request->leave_date;
        $leave->leave_time = $request->leave_time;
        $leave->type = $request->type;
        $leave->state = $request->state;
        $leave->status = $request->status;
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

    public static function overTimeDevice($att_dateTime, Employee $employee)
    {
        $attendance_time = new DateTime($att_dateTime);
        $checkout = new DateTime($employee->schedules->first()->time_out);
        $difference = $checkout->diff($attendance_time)->format('%H:%I:%S');

        $overtime = new Overtime();
        $overtime->emp_id = $employee->id;
        $overtime->duration = $difference;
        $overtime->overtime_date = date('Y-m-d', strtotime($att_dateTime));
        $overtime->save();
    }
}
