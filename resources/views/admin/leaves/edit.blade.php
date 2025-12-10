@extends('layouts.master')

@section('title')
Edit Leave Request
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Leave Request</h4>
            </div>
            <div class="card-body">
                @include('includes.flash')

                <form action="{{ route('leave.update', $leave->id_leave) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                @if(isset($currentEmployee) && $currentEmployee)
                                    {{-- Employee is logged in, show readonly field --}}
                                    <label for="emp_id">Employee <span class="text-danger">*</span></label>
                                    <input type="hidden" name="emp_id" value="{{ $currentEmployee->id_employees }}">
                                    <input type="text" class="form-control" value="{{ $currentEmployee->name }} ({{ $currentEmployee->employee_code ?? $currentEmployee->id_employees }})" readonly style="background-color: #e9ecef;">
                                    <small class="form-text text-muted">Your employee information is automatically filled</small>
                                @else
                                    {{-- Admin can select employee --}}
                                    <label for="emp_id">Employee <span class="text-danger">*</span></label>
                                    <select class="form-control @error('emp_id') is-invalid @enderror" id="emp_id" name="emp_id" required>
                                        <option value="">Select Employee</option>
                                        @foreach($employees as $employee)
                                        <option value="{{ $employee->id_employees }}" {{ (old('emp_id', $leave->emp_id) == $employee->id_employees) ? 'selected' : '' }}>
                                            {{ $employee->name }} ({{ $employee->employee_code ?? $employee->id_employees }})
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('emp_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="leave_date">Leave Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('leave_date') is-invalid @enderror"
                                    id="leave_date" name="leave_date"
                                    value="{{ old('leave_date', $leave->leave_date ? $leave->leave_date->format('Y-m-d') : '') }}" required>
                                @error('leave_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="leave_time">Leave Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('leave_time') is-invalid @enderror"
                                    id="leave_time" name="leave_time"
                                    value="{{ old('leave_time', $leave->leave_time ? $leave->leave_time->format('Y-m-d\TH:i') : '') }}" required>
                                @error('leave_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type">Leave Type <span class="text-danger">*</span></label>
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Select Leave Type</option>
                                    <option value="sick" {{ old('type', $leave->type) == 'sick' ? 'selected' : '' }}>Sick Leave</option>
                                    <option value="vacation" {{ old('type', $leave->type) == 'vacation' ? 'selected' : '' }}>Vacation Leave</option>
                                    <option value="personal" {{ old('type', $leave->type) == 'personal' ? 'selected' : '' }}>Personal Leave</option>
                                    <option value="emergency" {{ old('type', $leave->type) == 'emergency' ? 'selected' : '' }}>Emergency Leave</option>
                                    <option value="maternity" {{ old('type', $leave->type) == 'maternity' ? 'selected' : '' }}>Maternity Leave</option>
                                    <option value="paternity" {{ old('type', $leave->type) == 'paternity' ? 'selected' : '' }}>Paternity Leave</option>
                                    <option value="study" {{ old('type', $leave->type) == 'study' ? 'selected' : '' }}>Study Leave</option>
                                    <option value="other" {{ old('type', $leave->type) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="state">State/Reason</label>
                                <textarea class="form-control @error('state') is-invalid @enderror"
                                    id="state" name="state" rows="3"
                                    placeholder="Enter reason for leave">{{ old('state', $leave->state) }}</textarea>
                                @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @if(!isset($currentEmployee) || !$currentEmployee)
                        {{-- Only show status field for admin --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="pending" {{ old('status', $leave->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ old('status', $leave->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ old('status', $leave->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="cancelled" {{ old('status', $leave->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @else
                        {{-- Employee cannot change status --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <input type="text" class="form-control" value="{{ ucfirst($leave->status) }}" readonly style="background-color: #e9ecef;">
                                <small class="form-text text-muted">Status can only be changed by admin</small>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="form-group text-right">
                        <a href="{{ route('leave.index') }}" class="btn btn-secondary">Cancel</a>
                        <a href="{{ route('leave.show', $leave) }}" class="btn btn-info">View</a>
                        <button type="submit" class="btn btn-primary">Update Leave Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection