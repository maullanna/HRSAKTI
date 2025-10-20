<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Debug: Log the request
        \Log::info('Login attempt', [
            'email' => $request->email,
            'password' => $request->password,
            'ip' => $request->ip()
        ]);

        // Check if it's an admin login first
        if ($this->attemptAdminLogin($request)) {
            \Log::info('Admin login successful');
            return $this->sendLoginResponse($request);
        }

        // Check if it's an employee login
        if ($this->attemptEmployeeLogin($request)) {
            \Log::info('Employee login successful');
            return $this->sendEmployeeLoginResponse($request);
        }

        \Log::info('Login failed for both admin and employee');
        
        // If neither works, increment login attempts and return failed response
        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Attempt to log the user in as admin.
     */
    protected function attemptAdminLogin(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        
        if ($user && Hash::check($request->password, $user->password)) {
            Auth::guard('web')->login($user);
            return true;
        }
        
        return false;
    }

    /**
     * Attempt to log the user in as employee.
     */
    protected function attemptEmployeeLogin(Request $request)
    {
        $employee = Employee::where('email', $request->email)->first();
        
        \Log::info('Employee login attempt', [
            'email' => $request->email,
            'employee_found' => $employee ? 'yes' : 'no',
            'employee_id' => $employee ? $employee->id : null,
            'pin_code_exists' => $employee ? ($employee->pin_code ? 'yes' : 'no') : 'no'
        ]);
        
        if ($employee && Hash::check($request->password, $employee->pin_code)) {
            \Log::info('Employee password check successful');
            Auth::guard('employee')->login($employee);
            return true;
        }
        
        \Log::info('Employee password check failed');
        return false;
    }

    /**
     * Send the response after employee was authenticated.
     */
    protected function sendEmployeeLoginResponse(Request $request)
    {
        $request->session()->regenerate();
        $this->clearLoginAttempts($request);

        return redirect()->intended(route('admin'))->with('success', 'Welcome back, ' . Auth::guard('employee')->user()->name . '!');
    }

    /**
     * Get the failed login response instance.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }
}
