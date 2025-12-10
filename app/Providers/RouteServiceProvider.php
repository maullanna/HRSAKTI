<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/admin';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });

        // No rate limit for fingerprint sync automation
        RateLimiter::for('none', function (Request $request) {
            return Limit::none();
        });

        // Rate limiter untuk login (6 attempts per 10 menit per IP)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinutes(10, 6)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    // Hitung menit tersisa dengan aman
                    $minutes = 10; // Default 10 menit

                    if (isset($headers['X-RateLimit-Reset'])) {
                        $resetValue = $headers['X-RateLimit-Reset'];

                        // Cek apakah ini timestamp (lebih besar dari 1000000000) atau detik tersisa
                        if ($resetValue > 1000000000) {
                            // Ini adalah timestamp, hitung selisihnya
                            $secondsRemaining = max(0, $resetValue - time());
                            $minutes = ceil($secondsRemaining / 60);
                        } else {
                            // Ini adalah detik tersisa
                            $minutes = ceil($resetValue / 60);
                        }

                        // Pastikan menit antara 1-10 menit
                        $minutes = max(1, min(10, $minutes));
                    }

                    $message = "Terlalu banyak percobaan login. Coba lagi dalam {$minutes} menit. / Too many login attempts. Please try again in {$minutes} minutes.";

                    // Jika request adalah AJAX, return JSON
                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => $message,
                            'errors' => ['email' => [$message]],
                            'rate_limited' => true,
                            'wait_minutes' => $minutes
                        ], 429);
                    }

                    // Jika request biasa, redirect dengan error message dan flag rate_limited
                    return redirect()->back()
                        ->withErrors(['email' => $message])
                        ->with('rate_limited', true)
                        ->with('wait_minutes', $minutes)
                        ->withInput($request->only('email'));
                });
        });
    }
}
