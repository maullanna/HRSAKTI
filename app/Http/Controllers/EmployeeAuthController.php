<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $request->validate([
            'email' => 'required|email',
            'pin_code' => 'required|string',
        ]);

        $employee = Employee::where('email', $request->email)->first();

        if ($employee && Hash::check($request->pin_code, $employee->pin_code)) {
            Auth::guard('employee')->login($employee);
            
            // Flash message will be handled by redirect
            return redirect()->intended(route('employee.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle employee logout request.
     */
    public function logout(Request $request)
    {
        Auth::guard('employee')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Flash message will be handled by redirect
        return redirect()->route('employee.login');
    }

    /**
     * Show employee dashboard.
     */
    public function dashboard()
    {
        $employee = Auth::guard('employee')->user();
        return view('employee.dashboard', compact('employee'));
    }
}
