@extends('layouts.master')

@section('title')
    Add New Leave Request
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Add New Leave Request</h4>
            </div>
            <div class="card-body">
                @include('includes.flash')

                <form action="{{ route('leave.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="emp_id">Employee <span class="text-danger">*</span></label>
                                <select class="form-control @error('emp_id') is-invalid @enderror" id="emp_id" name="emp_id" required>
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('emp_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }} ({{ $employee->employee_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('emp_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="leave_date">Leave Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('leave_date') is-invalid @enderror" 
                                       id="leave_date" name="leave_date" value="{{ old('leave_date') }}" 
                                       min="{{ date('Y-m-d') }}" required>
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
                                       id="leave_time" name="leave_time" value="{{ old('leave_time') }}" required>
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
                                    <option value="sick" {{ old('type') == 'sick' ? 'selected' : '' }}>Sick Leave</option>
                                    <option value="vacation" {{ old('type') == 'vacation' ? 'selected' : '' }}>Vacation Leave</option>
                                    <option value="personal" {{ old('type') == 'personal' ? 'selected' : '' }}>Personal Leave</option>
                                    <option value="emergency" {{ old('type') == 'emergency' ? 'selected' : '' }}>Emergency Leave</option>
                                    <option value="maternity" {{ old('type') == 'maternity' ? 'selected' : '' }}>Maternity Leave</option>
                                    <option value="paternity" {{ old('type') == 'paternity' ? 'selected' : '' }}>Paternity Leave</option>
                                    <option value="study" {{ old('type') == 'study' ? 'selected' : '' }}>Study Leave</option>
                                    <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
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
                                          placeholder="Enter reason for leave">{{ old('state') }}</textarea>
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group text-right">
                        <a href="{{ route('leave.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Leave Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
