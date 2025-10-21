<?php



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FingerDevicesControlller;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Debug route - remove after testing
Route::get('/debug-employee', function () {
    $employee = \App\Models\Employee::where('email', 'maullanna35@gmail.com')->first();
    if ($employee) {
        return response()->json([
            'found' => true,
            'id' => $employee->id,
            'name' => $employee->name,
            'email' => $employee->email,
            'position' => $employee->position,
            'pin_code' => $employee->pin_code,
            'created_at' => $employee->created_at,
            'pin_code_length' => strlen($employee->pin_code)
        ]);
    }
    return response()->json(['found' => false, 'all_employees' => \App\Models\Employee::all(['id', 'name', 'email'])]);
});

// Debug password check
Route::get('/debug-password', function () {
    $employee = \App\Models\Employee::where('email', 'maullanna35@gmail.com')->first();
    if ($employee) {
        $testPassword = '45273';
        $isValid = \Illuminate\Support\Facades\Hash::check($testPassword, $employee->pin_code);
        return response()->json([
            'employee_found' => true,
            'test_password' => $testPassword,
            'stored_hash' => $employee->pin_code,
            'password_valid' => $isValid,
            'new_hash_for_45273' => \Illuminate\Support\Facades\Hash::make('45273')
        ]);
    }
    return response()->json(['employee_found' => false]);
});

// Debug admin role
Route::get('/debug-admin', function () {
    $admin = \App\Models\User::first();
    if ($admin) {
        $roles = $admin->roles()->get();
        $firstRole = $admin->roles()->first();
        $permissions = $firstRole ? json_decode($firstRole->permissions, true) : [];
        
        return response()->json([
            'admin_found' => true,
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
            'admin_email' => $admin->email,
            'roles_count' => $roles->count(),
            'roles' => $roles->pluck('name'),
            'first_role' => $firstRole ? $firstRole->name : 'No role',
            'permissions' => $permissions
        ]);
    }
    return response()->json(['admin_found' => false, 'all_users' => \App\Models\User::all(['id', 'name', 'email'])]);
});

// Clear cache via browser
Route::get('/clear-cache', function () {
    try {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('route:clear');
        \Artisan::call('view:clear');
        
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
Route::get('attended/{user_id}', '\App\Http\Controllers\AttendanceController@attended' )->name('attended');
Route::get('attended-before/{user_id}', '\App\Http\Controllers\AttendanceController@attendedBefore' )->name('attendedBefore');
Auth::routes(['register' => false, 'reset' => false]);

// Employee Routes
Route::post('/employee/logout', '\App\Http\Controllers\EmployeeAuthController@logout')->name('employee.logout');

Route::group(['middleware' => ['multi.auth']], function () {
    // Routes accessible by both admin and employee
    Route::get('/admin', '\App\Http\Controllers\AdminController@index')->name('admin');
    Route::get('/latetime', '\App\Http\Controllers\AttendanceController@indexLatetime')->name('indexLatetime');
    Route::get('/overtime', '\App\Http\Controllers\LeaveController@indexOvertime')->name('indexOvertime');
    
    // Super Admin & Admin SDM routes
    Route::group(['middleware' => ['Role:super_admin,admin_sdm']], function () {
        Route::resource('/employees', '\App\Http\Controllers\EmployeeController');
        Route::get('/check', '\App\Http\Controllers\CheckController@index')->name('check');
        Route::get('/sheet-report', '\App\Http\Controllers\CheckController@sheetReport')->name('sheet-report');
        Route::post('check-store','\App\Http\Controllers\CheckController@CheckStore')->name('check_store');
        
        // Fingerprint Devices
        Route::resource('/finger_device', '\App\Http\Controllers\BiometricDeviceController');
        Route::delete('finger_device/destroy', '\App\Http\Controllers\BiometricDeviceController@massDestroy')->name('finger_device.massDestroy');
        Route::get('finger_device/{fingerDevice}/employees/add', '\App\Http\Controllers\BiometricDeviceController@addEmployee')->name('finger_device.add.employee');
        Route::get('finger_device/{fingerDevice}/get/attendance', '\App\Http\Controllers\BiometricDeviceController@getAttendance')->name('finger_device.get.attendance');
        // Temp Clear Attendance route
        Route::get('finger_device/clear/attendance', function () {
            $midnight = \Carbon\Carbon::createFromTime(23, 50, 00);
            $diff = now()->diffInMinutes($midnight);
            dispatch(new ClearAttendanceJob())->delay(now()->addMinutes($diff));
            toast("Attendance Clearance Queue will run in 11:50 P.M}!", "success");

            return back();
        })->name('finger_device.clear.attendance');
    });

    // All authenticated users routes
    Route::group(['middleware' => ['Role:super_admin,admin_sdm,wadir,section,employee']], function () {
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
        Route::get('/overtime/requests', '\App\Http\Controllers\OvertimeController@requests')->name('overtime.requests');
        Route::get('/overtime/approvals', '\App\Http\Controllers\OvertimeController@approvals')->name('overtime.approvals');
        Route::get('/overtime/create', '\App\Http\Controllers\OvertimeController@create')->name('overtime.create');
        Route::post('/overtime', '\App\Http\Controllers\OvertimeController@store')->name('overtime.store');
        Route::get('/overtime/{overtime}', '\App\Http\Controllers\OvertimeController@show')->name('overtime.show');
        Route::get('/overtime/{overtime}/edit', '\App\Http\Controllers\OvertimeController@edit')->name('overtime.edit');
        Route::put('/overtime/{overtime}', '\App\Http\Controllers\OvertimeController@update')->name('overtime.update');
        Route::delete('/overtime/{overtime}', '\App\Http\Controllers\OvertimeController@destroy')->name('overtime.destroy');
        Route::post('/overtime/{overtime}/approve', '\App\Http\Controllers\OvertimeController@approve')->name('overtime.approve');
        Route::post('/overtime/{overtime}/reject', '\App\Http\Controllers\OvertimeController@reject')->name('overtime.reject');
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
        Route::get('/settings/debug', function() {
            return response()->json([
                'current_settings' => \App\Http\Controllers\SettingsController::getSettings(),
                'ams_name' => getSetting('ams_name'),
                'footer_text' => getFooterText()
            ]);
        })->name('settings.debug');
        
        // Admin Management routes
        Route::resource('/admin-management', '\App\Http\Controllers\AdminManagementController');
        
        Route::post('/settings/test-update', function(\Illuminate\Http\Request $request) {
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