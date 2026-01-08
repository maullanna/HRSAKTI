<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use RealRashid\SweetAlert\Facades\Alert;

class EmployeeAuthController extends Controller
{
    /**
     * Show the employee login form.
     */
    public function showLoginForm()
    {
        return view('auth.employee_login');
    }

    /**
     * Handle employee login request.
     */
    public function login(Request $request)
    {
        // Middleware throttle sudah handle blocking, kita hanya perlu reset counter saat berhasil
        $request->validate([
            'email' => 'required|email',
            'pin_code' => 'required|string',
        ]);

        $employee = Employee::where('email', $request->email)->first();

        if ($employee && Hash::check($request->pin_code, $employee->pin_code)) {
            // Login berhasil, reset rate limiter (gunakan key yang sama dengan middleware throttle)
            $this->clearLoginRateLimit($request);

            Auth::guard('employee')->login($employee);

            // Flash message will be handled by redirect
            return redirect()->intended(route('employee.dashboard'));
        }

        // Login gagal - counter sudah dihandle oleh middleware throttle
        return back()->withErrors([
            'email' => 'Email atau PIN code tidak sesuai. / The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle employee logout request.
     */
    public function logout(Request $request)
    {
        try {
            // Logout user
            Auth::guard('employee')->logout();

            // Regenerate CSRF token
            if ($request->hasSession()) {
                $request->session()->regenerateToken();
            }

            // Invalidate and regenerate session
            $request->session()->invalidate();
            $request->session()->regenerate();

            // Redirect to main login page
            return redirect()->route('login')->with('success', 'You have been logged out successfully.');
        } catch (\Exception $e) {
            // Log error for debugging
            Log::error('Employee logout error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // Even if there's an error, try to logout and redirect
            try {
                Auth::guard('employee')->logout();
            } catch (\Exception $e2) {
                // Ignore logout errors
            }

            // Redirect to main login page without flash message
            return redirect()->route('login');
        }
    }

    /**
     * Clear login rate limit untuk IP tertentu.
     * Key harus sama dengan yang digunakan oleh middleware throttle.
     */
    protected function clearLoginRateLimit(Request $request)
    {
        // Middleware throttle menggunakan key berdasarkan rate limiter 'login'
        // Coba beberapa format key yang mungkin digunakan
        $ip = $request->ip();
        $possibleKeys = [
            'throttle:login:' . $ip,
            'login:' . $ip,
            'throttle:login:' . md5($ip),
        ];

        // Clear semua kemungkinan key
        foreach ($possibleKeys as $key) {
            RateLimiter::clear($key);
        }
    }

    /**
     * Show employee dashboard.
     */
    public function dashboard()
    {
        $employee = Auth::guard('employee')->user();


        $currentMonth = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();


        $totalAttendance = \App\Models\Attendance::where('emp_id', $employee->id_employees)
            ->whereBetween('attendance_date', [$currentMonth->format('Y-m-d'), $currentMonthEnd->format('Y-m-d')])
            ->count();

        $onTimeAttendance = \App\Models\Attendance::where('emp_id', $employee->id_employees)
            ->whereBetween('attendance_date', [$currentMonth->format('Y-m-d'), $currentMonthEnd->format('Y-m-d')])
            ->where('status', 1)
            ->count();

        $lateAttendance = \App\Models\Attendance::where('emp_id', $employee->id_employees)
            ->whereBetween('attendance_date', [$currentMonth->format('Y-m-d'), $currentMonthEnd->format('Y-m-d')])
            ->where('status', 0)
            ->count();

        // Today's attendance
        $todayAttendance = \App\Models\Attendance::where('emp_id', $employee->id_employees)
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
        $recentAttendances = \App\Models\Attendance::where('emp_id', $employee->id_employees)
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
            'recentOvertimes'
        ));
    }
}
