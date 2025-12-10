<?php



use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\FingerDevicesControlller;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Language switch route
Route::get('/language/{locale}', '\App\Http\Controllers\LanguageController@switchLanguage')->name('language.switch');

// Clear cache via browser
Route::get('/clear-cache', function () {
    try {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        return response()->json([
            'success' => true,
            'message' => 'All cache cleared successfully',
            'commands' => [
                'cache:clear',
                'config:clear',
                'route:clear',
                'view:clear'
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => true,
            'message' => $e->getMessage()
        ]);
    }
});

// Fix admin role via browser
Route::get('/fix-admin-role', function () {
    try {
        // Create Super Admin role
        $role = \App\Models\Role::updateOrCreate(
            ['slug' => 'super_admin'],
            [
                'name' => 'Super Admin',
                'permissions' => '{"master_database":true,"absensi":true,"overtime":true,"cuti_tunjangan":true,"slip_gaji":true,"training_development":true,"reports":true,"settings":true}',
                'description' => 'Full access to all system features',
                'is_active' => true
            ]
        );

        // Get first admin user
        $admin = \App\Models\User::first();
        if (!$admin) {
            return response()->json(['error' => 'No admin user found']);
        }

        // Assign role to admin
        if (!$admin->roles()->where('role_id', $role->id)->exists()) {
            $admin->roles()->attach($role->id);
        }

        // Verify
        $adminRoles = $admin->roles()->get();

        return response()->json([
            'success' => true,
            'message' => 'Admin role fixed successfully',
            'admin' => $admin->name,
            'role' => $role->name,
            'permissions' => json_decode($role->permissions, true),
            'all_roles' => $adminRoles->pluck('name')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => true,
            'message' => $e->getMessage()
        ]);
    }
});
Route::get('attended/{user_id}', '\App\Http\Controllers\AttendanceController@attended')->name('attended');
Route::get('attended-before/{user_id}', '\App\Http\Controllers\AttendanceController@attendedBefore')->name('attendedBefore');
Auth::routes(['register' => false, 'reset' => false]);

// Employee Routes
Route::get('/employee/login', '\App\Http\Controllers\EmployeeAuthController@showLoginForm')->name('employee.login');
Route::post('/employee/login', '\App\Http\Controllers\EmployeeAuthController@login')
    ->middleware('throttle:login')
    ->name('employee.login.post');
Route::post('/employee/logout', '\App\Http\Controllers\EmployeeAuthController@logout')->name('employee.logout');

Route::group(['middleware' => ['multi.auth']], function () {
    // Routes accessible by both admin and employee
    Route::get('/admin', '\App\Http\Controllers\AdminController@index')->name('admin');
    Route::get('/latetime', '\App\Http\Controllers\AttendanceController@indexLatetime')->name('indexLatetime');
    Route::get('/overtime', '\App\Http\Controllers\LeaveController@indexOvertime')->name('indexOvertime');

    // Profile routes
    Route::get('/profile', '\App\Http\Controllers\ProfileController@show')->name('profile.show');
    Route::put('/profile', '\App\Http\Controllers\ProfileController@update')->name('profile.update');
    Route::post('/profile/upload-photo', '\App\Http\Controllers\ProfileController@uploadPhoto')->name('profile.upload-photo');
    Route::put('/profile/password', '\App\Http\Controllers\ProfileController@updatePassword')->name('profile.update-password');

    // Super Admin & Admin SDM routes
    Route::group(['middleware' => ['Role:super_admin,admin_sdm']], function () {
        Route::resource('/employees', '\App\Http\Controllers\EmployeeController');
        Route::get('/employees/import', '\App\Http\Controllers\EmployeeController@showImportForm')->name('employees.import.form');
        Route::post('/employees/import', '\App\Http\Controllers\EmployeeController@importEmployees')->name('employees.import');
        Route::get('/employees/template/download', '\App\Http\Controllers\EmployeeController@downloadEmployeeTemplate')->name('employees.download-template');
        Route::post('/employees/check-unique', '\App\Http\Controllers\EmployeeController@checkUnique')->name('employees.check-unique');

        // Fingerprint Devices
        Route::resource('/finger_device', '\App\Http\Controllers\BiometricDeviceController');
        Route::delete('finger_device/destroy', '\App\Http\Controllers\BiometricDeviceController@massDestroy')->name('finger_device.massDestroy');
        Route::get('finger_device/{fingerDevice}/employees/add', '\App\Http\Controllers\BiometricDeviceController@addEmployee')->name('finger_device.add.employee');
        Route::get('finger_device/{fingerDevice}/get/attendance', '\App\Http\Controllers\BiometricDeviceController@getAttendance')->name('finger_device.get.attendance');
    });

    // All authenticated users routes
    Route::group(['middleware' => ['Role:super_admin,admin_sdm,wadir,section,employee']], function () {
        Route::get('/check', '\App\Http\Controllers\CheckController@index')->name('check');
        Route::get('/sheet-report', '\App\Http\Controllers\CheckController@sheetReport')->name('sheet-report');
        Route::post('check-store', '\App\Http\Controllers\CheckController@CheckStore')->name('check_store');
        Route::get('/attendance', '\App\Http\Controllers\AttendanceController@index')->name('attendance');
        Route::get('/leave', '\App\Http\Controllers\LeaveController@index')->name('leave');
        Route::get('/overtime', '\App\Http\Controllers\LeaveController@indexOvertime')->name('indexOvertime');
        Route::resource('/trainings', '\App\Http\Controllers\TrainingController');
        Route::get('/reports/overtime', '\App\Http\Controllers\ReportController@overtimeReport')->name('reports.overtime');
        Route::get('/reports/export/overtime', '\App\Http\Controllers\ReportController@exportOvertimeReport')->name('reports.export.overtime');
    });

    // Leave routes - All authenticated users
    Route::group(['middleware' => ['Role:super_admin,admin_sdm,wadir,section,employee']], function () {
        Route::resource('/leave', '\App\Http\Controllers\LeaveController');
        Route::post('/leave/{leave}/approve', '\App\Http\Controllers\LeaveController@approve')->name('leave.approve');
        Route::post('/leave/{leave}/reject', '\App\Http\Controllers\LeaveController@reject')->name('leave.reject');
    });

    // Overtime routes - All authenticated users
    Route::group(['middleware' => ['Role:super_admin,admin_sdm,wadir,section,employee']], function () {
        // Overtime Reports routes
        Route::get('/overtime/reports', '\App\Http\Controllers\OvertimeController@index')->name('overtime.reports.index');
        Route::get('/overtime/reports/create', '\App\Http\Controllers\OvertimeController@create')->name('overtime.reports.create');
        Route::post('/overtime/reports', '\App\Http\Controllers\OvertimeController@store')->name('overtime.reports.store');
        Route::get('/overtime/reports/{overtime}', '\App\Http\Controllers\OvertimeController@show')->name('overtime.reports.show');
        Route::get('/overtime/reports/{overtime}/edit', '\App\Http\Controllers\OvertimeController@edit')->name('overtime.reports.edit');
        Route::put('/overtime/reports/{overtime}', '\App\Http\Controllers\OvertimeController@update')->name('overtime.reports.update');
        Route::delete('/overtime/reports/{overtime}', '\App\Http\Controllers\OvertimeController@destroy')->name('overtime.reports.destroy');

        // Overtime Requests routes
        Route::get('/overtime/requests', '\App\Http\Controllers\OvertimeController@requests')->name('overtime.requests');
        Route::get('/overtime/requests/create', '\App\Http\Controllers\OvertimeController@create')->name('overtime.requests.create');
        Route::post('/overtime/requests', '\App\Http\Controllers\OvertimeController@store')->name('overtime.requests.store');

        // Overtime Approvals routes
        Route::get('/overtime/approvals', '\App\Http\Controllers\OvertimeController@approvals')->name('overtime.approvals');
        Route::post('/overtime/{overtime}/approve', '\App\Http\Controllers\OvertimeController@approve')->name('overtime.approve');
        Route::post('/overtime/{overtime}/approve-section', '\App\Http\Controllers\OvertimeController@approveSection')->name('overtime.approve.section');
        Route::post('/overtime/{overtime}/approve-wadir', '\App\Http\Controllers\OvertimeController@approveWadir')->name('overtime.approve.wadir');
        Route::post('/overtime/{overtime}/approve-sdm', '\App\Http\Controllers\OvertimeController@approveSdm')->name('overtime.approve.sdm');
        Route::post('/overtime/{overtime}/reject', '\App\Http\Controllers\OvertimeController@reject')->name('overtime.reject');

        // Legacy routes for backward compatibility
        Route::get('/overtime/create', '\App\Http\Controllers\OvertimeController@create')->name('overtime.create');
        Route::post('/overtime', '\App\Http\Controllers\OvertimeController@store')->name('overtime.store');
        Route::get('/overtime/{overtime}', '\App\Http\Controllers\OvertimeController@show')->name('overtime.show');
        Route::get('/overtime/{overtime}/edit', '\App\Http\Controllers\OvertimeController@edit')->name('overtime.edit');
        Route::put('/overtime/{overtime}', '\App\Http\Controllers\OvertimeController@update')->name('overtime.update');
        Route::delete('/overtime/{overtime}', '\App\Http\Controllers\OvertimeController@destroy')->name('overtime.destroy');
    });

    // Salaries routes - Super Admin, Admin SDM, Employee
    Route::group(['middleware' => ['Role:super_admin,admin_sdm,employee']], function () {
        // Import routes must be defined before resource routes
        Route::get('/salaries/import', '\App\Http\Controllers\ImportController@showImportForm')->name('salaries.import');
        Route::post('/salaries/import', '\App\Http\Controllers\ImportController@importSalaries')->name('salaries.import');
        Route::get('/salaries/download-template', '\App\Http\Controllers\ImportController@downloadTemplate')->name('salaries.download-template');

        // Resource routes
        Route::resource('/salaries', '\App\Http\Controllers\SalaryController');
    });

    // Super Admin only routes
    Route::group(['middleware' => ['Role:super_admin']], function () {
        Route::get('/settings', '\App\Http\Controllers\SettingsController@index')->name('settings');
        Route::put('/settings', '\App\Http\Controllers\SettingsController@update')->name('settings.update');
        Route::get('/settings/debug', function () {
            return response()->json([
                'current_settings' => \App\Http\Controllers\SettingsController::getSettings(),
                'ams_name' => getSetting('ams_name'),
                'footer_text' => getFooterText()
            ]);
        })->name('settings.debug');

        // Admin Management routes
        Route::resource('/admin-management', '\App\Http\Controllers\AdminManagementController');

        Route::post('/settings/test-update', function (\Illuminate\Http\Request $request) {
            $controller = new \App\Http\Controllers\SettingsController();
            return $controller->update($request);
        })->name('settings.test-update');
    });
});

Route::group(['middleware' => ['auth']], function () {

    // Route::get('/home', 'HomeController@index')->name('home');





});

// Removed attendance/assign and leave/assign routes as they were causing redirect issues


// Route::get('{any}', 'App\http\controllers\VeltrixController@index');