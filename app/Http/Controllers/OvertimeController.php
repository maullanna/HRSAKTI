<?php

namespace App\Http\Controllers;

use App\Models\Overtime;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OvertimeController extends Controller
{
    /**
     * Display a listing of overtime requests.
     */
    public function index()
    {
        $overtimes = Overtime::with(['employee', 'section', 'sectionEmployee'])->latest();

        // Check if employee is logged in (section head)
        if (Auth::guard('employee')->check()) {
            $employee = Auth::guard('employee')->user();
            $position = $employee->position;

            // Section head: only see reports from their section's employees (karyawan and magang)
            if (strpos($position, 'Section ') === 0) {
                // Get all employees in this section (where id_section_employee = current section employee)
                $sectionEmployeeIds = Employee::where('id_section_employee', $employee->id_employees)
                    ->whereIn('position', ['Employees', 'Magang', 'PKL'])
                    ->pluck('id_employees')
                    ->toArray();

                // Filter overtimes from these employees
                $overtimes = $overtimes->whereIn('emp_id', $sectionEmployeeIds);
            } else {
                // Other employees: only see their own requests
                $overtimes = $overtimes->where('emp_id', $employee->id_employees);
            }
        } else {
            // Admin: see all overtime reports
            $user = Auth::guard('web')->user();
            if ($user && $user->hasRole('admin_sdm')) {
                // Admin SDM can see all
            } elseif ($user && $user->hasRole('super_admin')) {
                // Super admin can see all
            }
        }

        $overtimes = $overtimes->get();
        return view('admin.overtime.reports.index', compact('overtimes'));
    }

    /**
     * Display overtime requests for approval.
     */
    public function approvals()
    {
        $query = Overtime::with(['employee', 'section', 'sectionEmployee', 'wadirEmployee', 'sdmEmployee', 'directorEmployee', 'sectionApprover', 'wadirApprover', 'sdmApprover'])
            ->where('status', 'pending')
            ->latest();

        // Check if employee is logged in
        if (Auth::guard('employee')->check()) {
            $employee = Auth::guard('employee')->user();
            $position = $employee->position;

            // Filter based on employee position
            if (strpos($position, 'Section ') === 0) {
                // Section head: only see requests for their section
                // Filter where id_section_employee = current section employee's id
                $query->where('id_section_employee', $employee->id_employees)
                    ->where('section_approved', false);
            } elseif (in_array($position, ['Wadir 1', 'Wadir 2'])) {
                // Wadir: see requests that need wadir approval (already approved by section)
                $query->where('id_wadir_employee', $employee->id_employees)
                    ->where('section_approved', true)
                    ->where('wadir_approved', false);
            } elseif ($position === 'SDM/HRD') {
                // SDM: see requests that need SDM approval (already approved by section and wadir)
                $query->where('id_sdm_employee', $employee->id_employees)
                    ->where('section_approved', true)
                    ->where('wadir_approved', true)
                    ->where('sdm_approved', false);
            } else {
                // Other positions: no overtime approvals
                $query->whereRaw('1 = 0'); // Return empty result
            }
        } else {
            // Admin: see all pending requests
            $user = Auth::guard('web')->user();
            if ($user && $user->hasRole('admin_sdm')) {
                // SDM admin: see requests that need SDM approval
                $query->where('section_approved', true)
                    ->where('wadir_approved', true)
                    ->where('sdm_approved', false);
            }
            // Super admin: see all pending requests (no additional filter)
        }

        $overtimes = $query->get();
        return view('admin.overtime.approvals.index', compact('overtimes'));
    }

    /**
     * Display overtime requests.
     */
    public function requests()
    {
        // If employee is logged in, show only their requests
        if (Auth::guard('employee')->check()) {
            $employee = Auth::guard('employee')->user();
            $position = $employee->position;

            // Section tidak bisa melihat requests (hanya bisa melihat reports)
            if (strpos($position, 'Section ') === 0) {
                return redirect()->route('overtime.reports.index')
                    ->with('error', 'Section tidak dapat mengakses halaman requests. Silakan gunakan menu Overtime Reports untuk melihat report dari karyawan dan magang.');
            }

            $overtimes = Overtime::with(['employee', 'section', 'sectionEmployee', 'wadirEmployee', 'sdmEmployee', 'directorEmployee'])
                ->where('emp_id', $employee->id_employees)
                ->latest()
                ->get();
        } else {
            $overtimes = Overtime::with(['employee', 'section', 'sectionEmployee', 'wadirEmployee', 'sdmEmployee', 'directorEmployee'])->latest()->get();
        }
        return view('admin.overtime.requests.index', compact('overtimes'));
    }


    public function create()
    {
        // Cek apakah yang login adalah employee
        if (Auth::guard('employee')->check()) {
            $employee = Auth::guard('employee')->user();
            $position = $employee->position;

            // Section tidak bisa membuat request overtime
            if (strpos($position, 'Section ') === 0) {
                return redirect()->route('overtime.requests')
                    ->with('error', 'Section tidak dapat membuat request overtime. Hanya karyawan dan magang yang dapat membuat request.');
            }

            $employee->load(['section', 'sectionEmployee', 'wadirEmployee', 'sdmEmployee', 'directorEmployee']); // Load relationships

            // Check if section and sdm are set
            if (!$employee->id_section) {
                return redirect()->route('overtime.requests')
                    ->with('error', 'Section is not set for your account. Please contact administrator to update your data.');
            }

            if (!$employee->id_sdm_employee) {
                return redirect()->route('overtime.requests')
                    ->with('error', 'SDM/HRD is not set for your account. Please contact administrator to update your data.');
            }

            // Untuk employee, gunakan view khusus
            return view('admin.overtime.requests.create', compact('employee'));
        }

        // Untuk admin, tampilkan semua employee
        $employees = Employee::all();

        // Check if the request is from requests route (for employees)
        if (request()->is('overtime/requests/create') || request()->routeIs('overtime.requests.create')) {
            // Already handled above for employees
            if (Auth::guard('employee')->check()) {
                // Already returned view above
            } else {
                // For admin, use reports create view
                return view('admin.overtime.reports.create', compact('employees'));
            }
        }

        // Check if the request is from reports route
        if (request()->is('overtime/reports/create') || request()->routeIs('overtime.reports.create')) {
            return view('admin.overtime.reports.create', compact('employees'));
        }

        // Default: use requests create view for employees, reports create for admin
        if (Auth::guard('employee')->check()) {
            // Already handled above
        } else {
            return view('admin.overtime.reports.create', compact('employees'));
        }
    }

    /**
     * Store a newly created overtime request.
     */
    public function store(Request $request)
    {
        // Jika yang login adalah employee, auto-set employee_id
        $employee = null;
        if (Auth::guard('employee')->check()) {
            $employee = Auth::guard('employee')->user();
            $request->merge(['employee_id' => $employee->id_employees]);
        }

        $request->validate([
            'employee_id' => 'required|exists:employees,id_employees',
            'overtime_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'duration' => 'nullable|date_format:H:i:s',
            'reason' => 'required|string|max:500',
        ]);

        // Get employee data if not already loaded
        if (!$employee) {
            $employee = Employee::findOrFail($request->employee_id);
        }

        // Reload employee with relationships to ensure we have latest data
        $employee->refresh();
        $employee->load(['section', 'sectionEmployee', 'wadirEmployee', 'sdmEmployee', 'directorEmployee']);

        // Validate that employee has section
        if (!$employee->id_section) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Employee section is not set. Please update employee data in master data.');
        }

        // Auto-fill id_section_employee if not set: find section head for this section
        if (!$employee->id_section_employee && $employee->id_section) {
            $section = $employee->section;
            if ($section) {
                // Find section head: employee with position like "Section [section name]" and same id_section
                $sectionHead = Employee::where('id_section', $employee->id_section)
                    ->where('position', 'like', 'Section%')
                    ->first();

                if ($sectionHead) {
                    $employee->id_section_employee = $sectionHead->id_employees;
                    $employee->save();
                }
            }
        }

        // Validate that section employee is set after auto-fill attempt
        if (!$employee->id_section_employee) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Section Employee (Section Head) is not set for this employee. Please update employee data in master data or ensure a section head exists for this section.');
        }

        if (!$employee->id_wadir_employee) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Wadir is not set for this employee. Please update employee data in master data.');
        }

        if (!$employee->id_sdm_employee) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Employee SDM/HRD is not set. Please update employee data in master data.');
        }

        $overtime = new Overtime();
        $overtime->emp_id = $request->employee_id;
        $overtime->id_section = $employee->id_section;
        $overtime->id_section_employee = $employee->id_section_employee; // Section head (e.g., Akmal Amir)
        $overtime->id_wadir_employee = $employee->id_wadir_employee; // Wadir yang dipilih di organisasi
        $overtime->id_sdm_employee = $employee->id_sdm_employee;
        $overtime->id_director_employee = $employee->id_director_employee;
        $overtime->overtime_date = $request->overtime_date;
        $overtime->start_time = $request->start_time;
        $overtime->end_time = $request->end_time;

        // Calculate duration from start_time and end_time
        if ($request->start_time && $request->end_time) {
            $start = Carbon::parse($request->start_time);
            $end = Carbon::parse($request->end_time);
            $duration = $end->diff($start);
            $overtime->duration = sprintf('%02d:%02d:%02d', $duration->h, $duration->i, $duration->s);
        } else {
            $overtime->duration = $request->duration ?? '00:00:00';
        }
        $overtime->reason = $request->reason;
        $overtime->status = 'pending';
        $overtime->section_approved = false;
        $overtime->wadir_approved = false;
        $overtime->sdm_approved = false;
        $overtime->save();

        // Untuk employee, redirect ke requests
        if (Auth::guard('employee')->check()) {
            return redirect()->route('overtime.requests')->with('success', 'Overtime request submitted successfully!');
        }

        // Check if the request is from reports route
        if (request()->is('overtime/reports') || request()->routeIs('overtime.reports.store')) {
            return redirect()->route('overtime.reports.index')->with('success', 'Overtime request submitted successfully!');
        }

        return redirect()->route('overtime.requests')->with('success', 'Overtime request submitted successfully!');
    }

    /**
     * Display the specified overtime request.
     */
    public function show(Request $request, Overtime $overtime)
    {
        $overtime->load('employee');

        // Get the referrer route from query parameter or HTTP referrer
        $from = $request->query('from');

        if (!$from) {
            // Try to determine from HTTP referrer
            $referrer = $request->header('referer');
            if ($referrer) {
                // Parse the referrer URL to get the path
                $referrerPath = parse_url($referrer, PHP_URL_PATH);

                if ($referrerPath && strpos($referrerPath, '/overtime/approvals') !== false) {
                    $from = 'approvals';
                } elseif ($referrerPath && strpos($referrerPath, '/overtime/requests') !== false) {
                    $from = 'requests';
                } elseif ($referrerPath && strpos($referrerPath, '/overtime/reports') !== false) {
                    $from = 'reports';
                } elseif ($referrerPath && ($referrerPath === '/overtime' || preg_match('#^/overtime$#', $referrerPath))) {
                    $from = 'index';
                }
            }
        }

        // Check if the request is from reports route
        if (request()->is('overtime/reports/*') || request()->routeIs('overtime.reports.show')) {
            // Default to reports if still not determined
            if (!$from) {
                $from = 'reports';
            }
            return view('admin.overtime.reports.show', compact('overtime', 'from'));
        }

        // Default to requests if still not determined
        if (!$from) {
            $from = 'requests';
        }

        return view('admin.overtime.requests.show', compact('overtime', 'from'));
    }

    /**
     * Show the form for editing the specified overtime request.
     */
    public function edit(Overtime $overtime)
    {
        $employees = Employee::all();
        // Check if the request is from reports route
        if (request()->is('overtime/reports/*/edit') || request()->routeIs('overtime.reports.edit')) {
            return view('admin.overtime.reports.edit', compact('overtime', 'employees'));
        }
        return view('admin.overtime.reports.edit', compact('overtime', 'employees'));
    }

    /**
     * Update the specified overtime request.
     */
    public function update(Request $request, Overtime $overtime)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id_employees',
            'overtime_date' => 'required|date',
            'duration' => 'required|date_format:H:i:s',
            'reason' => 'required|string|max:500',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $overtime->emp_id = $request->employee_id;
        $overtime->overtime_date = $request->overtime_date;
        $overtime->duration = $request->duration;
        $overtime->reason = $request->reason;
        $overtime->status = $request->status;
        $overtime->save();

        // Check if the request is from reports route
        if (request()->is('overtime/reports/*') || request()->routeIs('overtime.reports.update')) {
            return redirect()->route('overtime.reports.index')->with('success', 'Overtime request updated successfully!');
        }

        return redirect()->route('overtime.requests')->with('success', 'Overtime request updated successfully!');
    }

    /**
     * Approve an overtime request by section.
     */
    public function approveSection(Overtime $overtime)
    {
        $employee = Auth::guard('employee')->check() ? Auth::guard('employee')->user() : null;
        $userId = $employee ? $employee->id_employees : auth()->id();

        $overtime->section_approved = true;
        $overtime->section_approved_by = $userId;
        $overtime->section_approved_at = now();

        // Status masih pending karena belum wadir & SDM approve
        $overtime->save();

        return redirect()->route('overtime.approvals')->with('success', 'Overtime request approved by Section successfully!');
    }

    /**
     * Approve an overtime request by wadir.
     */
    public function approveWadir(Overtime $overtime)
    {
        // Validasi: Wadir hanya bisa approve jika section sudah approve
        // Kecuali super_admin yang bisa bypass
        $user = Auth::guard('web')->user();
        $isSuperAdmin = $user && $user->hasRole('super_admin');

        if (!$isSuperAdmin && !$overtime->section_approved) {
            return redirect()->route('overtime.approvals')
                ->with('error', 'Section approval is required before Wadir can approve.');
        }

        $employee = Auth::guard('employee')->check() ? Auth::guard('employee')->user() : null;
        $userId = $employee ? $employee->id_employees : auth()->id();

        $overtime->wadir_approved = true;
        $overtime->wadir_approved_by = $userId;
        $overtime->wadir_approved_at = now();

        // Status masih pending karena belum SDM approve
        $overtime->save();

        return redirect()->route('overtime.approvals')->with('success', 'Overtime request approved by Wadir successfully!');
    }

    /**
     * Approve an overtime request by SDM.
     */
    public function approveSdm(Overtime $overtime)
    {
        // Validasi: SDM hanya bisa approve jika section dan wadir sudah approve
        // Kecuali super_admin yang bisa bypass
        $user = Auth::guard('web')->user();
        $isSuperAdmin = $user && $user->hasRole('super_admin');

        if (!$isSuperAdmin && (!$overtime->section_approved || !$overtime->wadir_approved)) {
            return redirect()->route('overtime.approvals')
                ->with('error', 'Section and Wadir approvals are required before SDM can approve.');
        }

        $employee = Auth::guard('employee')->check() ? Auth::guard('employee')->user() : null;
        $userId = $employee ? $employee->id_employees : auth()->id();

        $overtime->sdm_approved = true;
        $overtime->sdm_approved_by = $userId;
        $overtime->sdm_approved_at = now();

        // Jika semua sudah approve (section, wadir, sdm), status jadi approved
        if ($overtime->section_approved && $overtime->wadir_approved && $overtime->sdm_approved) {
            $overtime->status = 'approved';
            $overtime->approved_by = $userId;
            $overtime->approved_at = now();
        }

        $overtime->save();

        return redirect()->route('overtime.approvals')->with('success', 'Overtime request approved by SDM successfully!');
    }

    /**
     * Approve an overtime request (legacy method for backward compatibility).
     */
    public function approve(Overtime $overtime)
    {
        $user = Auth::guard('web')->user();

        // Determine approval type based on user role
        if ($user && $user->hasRole('section')) {
            return $this->approveSection($overtime);
        } elseif ($user && $user->hasRole('admin_sdm')) {
            return $this->approveSdm($overtime);
        } else {
            // For super_admin or other roles, approve both
            $overtime->section_approved = true;
            $overtime->section_approved_by = auth()->id();
            $overtime->section_approved_at = now();
            $overtime->sdm_approved = true;
            $overtime->sdm_approved_by = auth()->id();
            $overtime->sdm_approved_at = now();
            $overtime->status = 'approved';
            $overtime->approved_by = auth()->id();
            $overtime->approved_at = now();
            $overtime->save();

            return redirect()->route('overtime.approvals')->with('success', 'Overtime request approved successfully!');
        }
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

        // Check if the request is from reports route
        if (request()->is('overtime/reports/*') || request()->routeIs('overtime.reports.destroy')) {
            return redirect()->route('overtime.reports.index')->with('success', 'Overtime request deleted successfully!');
        }

        return redirect()->route('overtime.requests')->with('success', 'Overtime request deleted successfully!');
    }
}
