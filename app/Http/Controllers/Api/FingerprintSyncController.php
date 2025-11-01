<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Http\Controllers\AttendanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
            'pin' => 'required|integer',
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

        $pin = $request->input('pin');
        $datetime = $request->input('datetime');
        $verified = $request->input('verified', 15);
        $status = $request->input('status', 0);

        try {
            // Find employee by PIN (PIN fingerprint = id_employees)
            // Note: Pastikan PIN di fingerprint device = id_employees di database
            $employee = Employee::where('id_employees', $pin)->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => "Employee with PIN {$pin} not found"
                ], 404);
            }

            // Parse datetime
            $attendanceDateTime = Carbon::parse($datetime);
            $attendanceDate = $attendanceDateTime->format('Y-m-d');
            $attendanceTime = $attendanceDateTime->format('H:i:s');

            // Check if attendance already exists (prevent duplicate)
            $existingAttendance = Attendance::where('attendance_date', $attendanceDate)
                ->where('emp_id', $employee->id_employees)
                ->where('type', 0) // 0 = check-in
                ->first();

            if ($existingAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance already exists for this date',
                    'data' => [
                        'attendance_id' => $existingAttendance->id,
                        'attendance_date' => $attendanceDate,
                        'attendance_time' => $existingAttendance->attendance_time
                    ]
                ], 409); // Conflict
            }

            // Get employee schedule to check on-time/late
            $schedule = $employee->schedules->first();
            $isOnTime = true;
            
            if ($schedule && $schedule->time_in) {
                // Compare attendance_time with schedule time_in
                $scheduleTime = Carbon::parse($attendanceDate . ' ' . $schedule->time_in);
                $attendanceTimeCarbon = Carbon::parse($attendanceDate . ' ' . $attendanceTime);
                
                // If attendance time is later than schedule time, mark as late
                if ($attendanceTimeCarbon->gt($scheduleTime)) {
                    $isOnTime = false;
                }
            }

            // Create attendance record
            $attendance = new Attendance();
            $attendance->uid = 0; // From fingerprint device
            $attendance->emp_id = $employee->id_employees;
            $attendance->state = $status;
            $attendance->attendance_time = $attendanceTime;
            $attendance->attendance_date = $attendanceDate;
            $attendance->status = $isOnTime ? 1 : 0; // 1 = on-time, 0 = late
            $attendance->type = 0; // 0 = check-in
            
            // If late, record latetime
            if (!$isOnTime && $schedule) {
                $difference = $attendanceTimeCarbon->diff($scheduleTime);
                $lateDuration = $difference->format('%H:%I:%S');
                
                // Call latetime function if exists
                if (method_exists(AttendanceController::class, 'lateTimeDevice')) {
                    AttendanceController::lateTimeDevice($datetime, $employee);
                }
            }
            
            $attendance->save();

            return response()->json([
                'success' => true,
                'message' => 'Attendance synced successfully',
                'data' => [
                    'attendance_id' => $attendance->id,
                    'employee_name' => $employee->name,
                    'attendance_date' => $attendanceDate,
                    'attendance_time' => $attendanceTime,
                    'status' => $isOnTime ? 'on-time' : 'late',
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
            'attendances.*.pin' => 'required|integer',
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
                        'pin' => $attendanceData['pin'] ?? null,
                        'error' => $result['message']
                    ];
                }
            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = [
                    'index' => $index,
                    'pin' => $attendanceData['pin'] ?? null,
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
        $pin = $attendanceData['pin'];
        $datetime = $attendanceData['datetime'];
        $verified = $attendanceData['verified'] ?? 15;
        $status = $attendanceData['status'] ?? 0;

        // Find employee
        $employee = Employee::where('id_employees', $pin)->first();

        if (!$employee) {
            return [
                'success' => false,
                'message' => "Employee with PIN {$pin} not found"
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
        $schedule = $employee->schedules->first();
        $isOnTime = true;
        
        if ($schedule && $schedule->time_in) {
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

