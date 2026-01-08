@extends('layouts.master')

@section('css')
<style>
    /* Fix button styling to prevent vertical text wrapping */
    .btn {
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 8px 16px;
    }
    
    .btn i {
        margin-right: 5px;
        line-height: 1;
    }
    
    /* Ensure button container doesn't cause wrapping */
    .d-flex.justify-content-end {
        flex-wrap: nowrap;
    }
</style>
@endsection

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Edit Attendance</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Attendance</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</div>
@endsection

@section('button')
<div class="col-sm-6">
    <div class="d-flex justify-content-end">
        <a href="{{ route('attendance') }}" class="btn btn-secondary" style="white-space: nowrap; padding: 8px 16px;">
            <i class="mdi mdi-arrow-left"></i> Back to List
        </a>
    </div>
</div>
@endsection

@section('content')
@include('includes.flash')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title mb-4">Edit Attendance Record</h4>

                <form action="{{ route('attendance.update', $attendance->id_attendance) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employee_name">Employee Name</label>
                                <input type="text" class="form-control" id="employee_name" 
                                       value="{{ $attendance->employee ? $attendance->employee->name : 'N/A' }}" 
                                       readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="emp_id">Employee ID</label>
                                <input type="text" class="form-control" id="emp_id" 
                                       value="{{ $attendance->emp_id }}" 
                                       readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="attendance_date">Date <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control @error('attendance_date') is-invalid @enderror" 
                                       id="attendance_date" 
                                       name="attendance_date" 
                                       value="{{ old('attendance_date', $attendance->attendance_date) }}" 
                                       required>
                                @error('attendance_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="attendance_time">Time In <span class="text-danger">*</span></label>
                                <input type="time" 
                                       class="form-control @error('attendance_time') is-invalid @enderror" 
                                       id="attendance_time" 
                                       name="attendance_time" 
                                       value="{{ old('attendance_time', $attendance->attendance_time ? \Carbon\Carbon::parse($attendance->attendance_time)->format('H:i') : '') }}" 
                                       required>
                                @error('attendance_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="time_out">Time Out</label>
                                <input type="time" 
                                       class="form-control @error('time_out') is-invalid @enderror" 
                                       id="time_out" 
                                       name="time_out" 
                                       value="{{ old('time_out', ($timeOut && $timeOut->attendance_time) ? \Carbon\Carbon::parse($timeOut->attendance_time)->format('H:i') : '') }}">
                                @error('time_out')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Leave empty if no time out recorded</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" 
                                        id="status" 
                                        name="status" 
                                        required>
                                    <option value="1" {{ old('status', $attendance->status) == 1 ? 'selected' : '' }}>On Time</option>
                                    <option value="0" {{ old('status', $attendance->status) == 0 ? 'selected' : '' }}>Late</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" style="white-space: nowrap;">
                            <i class="mdi mdi-content-save"></i> Update Attendance
                        </button>
                        <a href="{{ route('attendance') }}" class="btn btn-secondary" style="white-space: nowrap;">
                            <i class="mdi mdi-close"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Format time input to show seconds
        $('#attendance_time, #time_out').on('change', function() {
            var time = $(this).val();
            if (time && !time.includes(':')) {
                // If only hour is entered, add :00
                if (time.length === 2) {
                    $(this).val(time + ':00');
                }
            }
        });
    });
</script>
@endsection

