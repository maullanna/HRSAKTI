<?php

namespace App\Http\Controllers;

use App\Models\Overtime;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OvertimeController extends Controller
{
    /**
     * Display a listing of overtime requests.
     */
    public function index()
    {
        $overtimes = Overtime::with('employee')->latest()->get();
        return view('admin.overtime.index', compact('overtimes'));
    }

    /**
     * Display overtime requests for approval.
     */
    public function approvals()
    {
        $overtimes = Overtime::with('employee')
            ->where('status', 'pending')
            ->latest()
            ->get();
        return view('admin.overtime.approvals', compact('overtimes'));
    }

    /**
     * Display overtime requests.
     */
    public function requests()
    {
        $overtimes = Overtime::with('employee')->latest()->get();
        return view('admin.overtime.requests', compact('overtimes'));
    }

    /**
     * Show the form for creating a new overtime request.
     */
    public function create()
    {
        $employees = Employee::all();
        return view('admin.overtime.create', compact('employees'));
    }

    /**
     * Store a newly created overtime request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'overtime_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required|string|max:500',
        ]);

        $overtime = new Overtime();
        $overtime->emp_id = $request->employee_id;
        $overtime->overtime_date = $request->overtime_date;
        $overtime->start_time = $request->start_time;
        $overtime->end_time = $request->end_time;
        $overtime->reason = $request->reason;
        $overtime->status = 'pending';
        $overtime->save();

        return redirect()->route('overtime.requests')->with('success', 'Overtime request submitted successfully!');
    }

    /**
     * Display the specified overtime request.
     */
    public function show(Overtime $overtime)
    {
        $overtime->load('employee');
        return view('admin.overtime.show', compact('overtime'));
    }

    /**
     * Show the form for editing the specified overtime request.
     */
    public function edit(Overtime $overtime)
    {
        $employees = Employee::all();
        return view('admin.overtime.edit', compact('overtime', 'employees'));
    }

    /**
     * Update the specified overtime request.
     */
    public function update(Request $request, Overtime $overtime)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'overtime_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required|string|max:500',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $overtime->emp_id = $request->employee_id;
        $overtime->overtime_date = $request->overtime_date;
        $overtime->start_time = $request->start_time;
        $overtime->end_time = $request->end_time;
        $overtime->reason = $request->reason;
        $overtime->status = $request->status;
        $overtime->save();

        return redirect()->route('overtime.requests')->with('success', 'Overtime request updated successfully!');
    }

    /**
     * Approve an overtime request.
     */
    public function approve(Overtime $overtime)
    {
        $overtime->status = 'approved';
        $overtime->approved_by = auth()->id();
        $overtime->approved_at = now();
        $overtime->save();

        return redirect()->route('overtime.approvals')->with('success', 'Overtime request approved successfully!');
    }

    /**
     * Reject an overtime request.
     */
    public function reject(Overtime $overtime)
    {
        $overtime->status = 'rejected';
        $overtime->approved_by = auth()->id();
        $overtime->approved_at = now();
        $overtime->save();

        return redirect()->route('overtime.approvals')->with('success', 'Overtime request rejected successfully!');
    }

    /**
     * Remove the specified overtime request.
     */
    public function destroy(Overtime $overtime)
    {
        $overtime->delete();
        return redirect()->route('overtime.requests')->with('success', 'Overtime request deleted successfully!');
    }
}
