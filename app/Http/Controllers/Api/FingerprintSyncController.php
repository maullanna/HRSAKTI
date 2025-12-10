<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Http\Controllers\AttendanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FingerprintSyncController extends Controller
{
    /**
     * Sync attendance data from fingerprint device
     * Called by Node.js service
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sync(Request $request)
    {
        // Validate API token if configured
        $apiToken = $request->header('Authorization');
        if ($apiToken && strpos($apiToken, 'Bearer ') === 0) {
            $apiToken = substr($apiToken, 7); // Remove 'Bearer ' prefix
        }
        $apiToken = $apiToken ?? $request->input('api_token');

        $expectedToken = env('FINGERPRINT_API_TOKEN');

        // If token is configured, validate it. If not configured, allow without token (for development)
        if ($expectedToken && $apiToken !== $expectedToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid or missing API token'
            ], 401);
        }

        // Validate request data
        $validator = Validator::make($request->all(), [
            'no_id' => 'required|string', // No ID dari fingerprint device
            'datetime' => 'required|date',
            'verified' => 'nullable|integer',
            'status' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $noId = $request->input('no_id');
        $datetime = $request->input('datetime');
        $verified = $request->input('verified', 15);
        $status = $request->input('status', 0);

        try {
            // Log incoming request for debugging
            Log::info('Fingerprint sync request received', [
                'no_id' => $noId,
                'datetime' => $datetime,
                'verified' => $verified,
                'status' => $status
            ]);

            // Find employee by No ID (No ID fingerprint = employee_code)
            // Note: No ID di fingerprint device harus sama dengan employee_code
            $employee = Employee::where('employee_code', $noId)->first();

            if (!$employee) {
                Log::warning('Employee not found during fingerprint sync', [
                    'no_id' => $noId,
                    'datetime' => $datetime
                ]);
                return response()->json([
                    'success' => false,
                    'message' => "Employee with code {$noId} not found"
                ], 404);
            }

            Log::info('Employee found for fingerprint sync', [
                'no_id' => $noId,
                'employee_id' => $employee->id_employees,
                'employee_name' => $employee->name,
                'employee_code' => $employee->employee_code
            ]);

            // Parse datetime
            $attendanceDateTime = Carbon::parse($datetime);
            $attendanceDate = $attendanceDateTime->format('Y-m-d');
            $attendanceTime = $attendanceDateTime->format('H:i:s');

            // Determine if this is check-in or check-out
            // Logic: 
            // - If no existing check-in: treat as check-in (type = 0)
            // - If check-in exists but no check-out: treat as check-out (type = 1)
            // - If both exist: prevent duplicate
            $existingCheckIn = Attendance::where('attendance_date', $attendanceDate)
                ->where('emp_id', $employee->id_employees)
                ->where('type', 0) // 0 = check-in
                ->first();

            $existingCheckOut = Attendance::where('attendance_date', $attendanceDate)
                ->where('emp_id', $employee->id_employees)
                ->where('type', 1) // 1 = check-out
                ->first();

            // Determine attendance type
            $attendanceType = 0; // Default: check-in
            if ($existingCheckIn && !$existingCheckOut) {
                // If check-in exists but no check-out yet, this is check-out
                $attendanceType = 1; // Check-out
            } elseif (!$existingCheckIn) {
                // If no check-in exists, this is check-in
                $attendanceType = 0; // Check-in
            } else {
                // Both exist, will be handled by duplicate check below
                $attendanceType = 0; // Default, but will be caught as duplicate
            }

            // Check if this type already exists (prevent duplicate)
            $existingAttendance = Attendance::where('attendance_date', $attendanceDate)
                ->where('emp_id', $employee->id_employees)
                ->where('type', $attendanceType)
                ->first();

            if ($existingAttendance) {
                Log::info('Attendance already exists (duplicate prevented)', [
                    'employee_code' => $employee->employee_code,
                    'employee_id' => $employee->id_employees,
                    'attendance_date' => $attendanceDate,
                    'attendance_type' => $attendanceType == 0 ? 'check-in' : 'check-out',
                    'existing_attendance_id' => $existingAttendance->id_attendance,
                    'existing_time' => $existingAttendance->attendance_time,
                    'new_time' => $attendanceTime
                ]);
                return response()->json([
                    'success' => false,
                    'message' => ucfirst($attendanceType == 0 ? 'check-in' : 'check-out') . ' already exists for this date',
                    'data' => [
                        'attendance_id' => $existingAttendance->id_attendance,
                        'attendance_date' => $attendanceDate,
                        'attendance_time' => $existingAttendance->attendance_time,
                        'type' => $attendanceType == 0 ? 'check-in' : 'check-out'
                    ]
                ], 409); // Conflict
            }

            // Get employee schedule to check on-time/late
            // DISABLED: Schedule checking temporarily disabled (Schedule model not found)
            // TODO: Enable this after Schedule model is created
            $isOnTime = true; // Default: all attendance marked as on-time

            // Uncomment below when Schedule model is ready:
            /*
            try {
                if (method_exists($employee, 'schedules')) {
                    $schedule = $employee->schedules()->first();
                    if ($schedule && isset($schedule->time_in)) {
                        $scheduleTime = Carbon::parse($attendanceDate . ' ' . $schedule->time_in);
                        $attendanceTimeCarbon = Carbon::parse($attendanceDate . ' ' . $attendanceTime);
                        
                        if ($attendanceTimeCarbon->gt($scheduleTime)) {
                            $isOnTime = false;
                            
                            // Call latetime function if exists
                            if (method_exists(AttendanceController::class, 'lateTimeDevice')) {
                                AttendanceController::lateTimeDevice($datetime, $employee);
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::warning("Schedule check failed for employee {$employee->id_employees}: " . $e->getMessage());
            }
            */

            // Create attendance record
            $attendance = new Attendance();
            $attendance->uid = 0; // From fingerprint device
            $attendance->emp_id = $employee->id_employees;
            $attendance->state = $status;
            $attendance->attendance_time = $attendanceTime;
            $attendance->attendance_date = $attendanceDate;
            $attendance->status = $isOnTime ? 1 : 0; // 1 = on-time, 0 = late (only for check-in)
            $attendance->type = $attendanceType; // 0 = check-in, 1 = check-out

            $attendance->save();

            // Log successful attendance creation
            Log::info('Attendance created successfully', [
                'attendance_id' => $attendance->id_attendance,
                'employee_id' => $attendance->emp_id,
                'employee_code' => $employee->employee_code,
                'attendance_date' => $attendanceDate,
                'attendance_time' => $attendanceTime,
                'attendance_type' => $attendanceType == 0 ? 'check-in' : 'check-out',
                'status' => $attendance->status
            ]);

            return response()->json([
                'success' => true,
                'message' => ucfirst($attendanceType == 0 ? 'check-in' : 'check-out') . ' synced successfully',
                'data' => [
                    'attendance_id' => $attendance->id_attendance,
                    'employee_id' => $employee->id_employees,
                    'employee_name' => $employee->name,
                    'employee_code' => $employee->employee_code,
                    'attendance_date' => $attendanceDate,
                    'attendance_time' => $attendanceTime,
                    'attendance_type' => $attendanceType == 0 ? 'check-in' : 'check-out',
                    'status' => $attendanceType == 0 ? ($isOnTime ? 'on-time' : 'late') : 'checked-out',
                    'status_code' => $attendance->status
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync multiple attendance records at once
     * For bulk sync from Node.js
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncBulk(Request $request)
    {
        // Validate API token if configured
        $apiToken = $request->header('Authorization');
        if ($apiToken && strpos($apiToken, 'Bearer ') === 0) {
            $apiToken = substr($apiToken, 7);
        }
        $apiToken = $apiToken ?? $request->input('api_token');

        $expectedToken = env('FINGERPRINT_API_TOKEN');

        if ($expectedToken && $apiToken !== $expectedToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid or missing API token'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'attendances' => 'required|array',
            'attendances.*.no_id' => 'required|string', // No ID dari fingerprint device
            'attendances.*.datetime' => 'required|date',
            'attendances.*.verified' => 'nullable|integer',
            'attendances.*.status' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $attendances = $request->input('attendances');
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($attendances as $index => $attendanceData) {
            try {
                $result = $this->processSingleAttendance($attendanceData);
                if ($result['success']) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $errors[] = [
                        'index' => $index,
                        'no_id' => $attendanceData['no_id'] ?? null,
                        'error' => $result['message']
                    ];
                }
            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = [
                    'index' => $index,
                    'no_id' => $attendanceData['no_id'] ?? null,
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Synced {$successCount} attendances, {$errorCount} errors",
            'data' => [
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'errors' => $errors
            ]
        ], 200);
    }

    /**
     * Process single attendance record
     * Helper method for bulk sync
     *
     * @param array $attendanceData
     * @return array
     */
    private function processSingleAttendance($attendanceData)
    {
        $noId = $attendanceData['no_id'];
        $datetime = $attendanceData['datetime'];
        $verified = $attendanceData['verified'] ?? 15;
        $status = $attendanceData['status'] ?? 0;

        // Find employee by employee_code (No ID = employee_code)
        $employee = Employee::where('employee_code', $noId)->first();

        if (!$employee) {
            return [
                'success' => false,
                'message' => "Employee with code {$noId} not found"
            ];
        }

        // Parse datetime
        $attendanceDateTime = Carbon::parse($datetime);
        $attendanceDate = $attendanceDateTime->format('Y-m-d');
        $attendanceTime = $attendanceDateTime->format('H:i:s');

        // Check duplicate
        $existingAttendance = Attendance::where('attendance_date', $attendanceDate)
            ->where('emp_id', $employee->id_employees)
            ->where('type', 0)
            ->first();

        if ($existingAttendance) {
            return [
                'success' => false,
                'message' => 'Attendance already exists'
            ];
        }

        // Check on-time/late
        // DISABLED: Schedule checking temporarily disabled (Schedule model not found)
        $isOnTime = true; // Default: all attendance marked as on-time

        // Uncomment below when Schedule model is ready:
        /*
        try {
            if (method_exists($employee, 'schedules')) {
                $schedule = $employee->schedules()->first();
                if ($schedule && isset($schedule->time_in)) {
                    $scheduleTime = Carbon::parse($attendanceDate . ' ' . $schedule->time_in);
                    $attendanceTimeCarbon = Carbon::parse($attendanceDate . ' ' . $attendanceTime);

                    if ($attendanceTimeCarbon->gt($scheduleTime)) {
                        $isOnTime = false;

                        // Record latetime
                        if (method_exists(AttendanceController::class, 'lateTimeDevice')) {
                            AttendanceController::lateTimeDevice($datetime, $employee);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::warning("Schedule check failed for employee {$employee->id_employees}: " . $e->getMessage());
        }
        */

        // Save attendance
        $attendance = new Attendance();
        $attendance->uid = 0;
        $attendance->emp_id = $employee->id_employees;
        $attendance->state = $status;
        $attendance->attendance_time = $attendanceTime;
        $attendance->attendance_date = $attendanceDate;
        $attendance->status = $isOnTime ? 1 : 0;
        $attendance->type = 0;
        $attendance->save();

        return [
            'success' => true,
            'message' => 'Attendance synced',
            'attendance_id' => $attendance->id
        ];
    }
}
