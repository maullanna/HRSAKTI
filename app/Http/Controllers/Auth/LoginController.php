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
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
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
        // Gunakan middleware throttle untuk benar-benar blokir request
        $this->middleware('throttle:login')->only('login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        // Middleware throttle sudah handle blocking, kita hanya perlu reset counter saat berhasil
        $this->validateLogin($request);

        // Check if it's an admin login first
        if ($this->attemptAdminLogin($request)) {
            // Login berhasil, reset rate limiter (gunakan key yang sama dengan middleware throttle)
            $this->clearLoginRateLimit($request);
            return $this->sendLoginResponse($request);
        }

        // Check if it's an employee login
        if ($this->attemptEmployeeLogin($request)) {
            // Login berhasil, reset rate limiter (gunakan key yang sama dengan middleware throttle)
            $this->clearLoginRateLimit($request);
            return $this->sendEmployeeLoginResponse($request);
        }

        // Login gagal - counter sudah dihandle oleh middleware throttle
        // Return failed response
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

        if ($employee && Hash::check($request->password, $employee->pin_code)) {
            Auth::guard('employee')->login($employee);
            return true;
        }

        return false;
    }

    /**
     * Send the response after the user was authenticated (for admin).
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();
        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        return redirect()->intended($this->redirectPath())->with('welcome', 'Selamat Datang');
    }

    /**
     * Send the response after employee was authenticated.
     */
    protected function sendEmployeeLoginResponse(Request $request)
    {
        $request->session()->regenerate();
        $this->clearLoginAttempts($request);

        return redirect()->intended(route('admin'))->with('welcome', 'Selamat Datang');
    }

    /**
     * Get the failed login response instance.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return back()->withErrors([
            'email' => 'Email atau password tidak sesuai. / The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
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
}
