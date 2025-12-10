@extends('layouts.master')

@section('css')
@endsection

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Edit Overtime Report</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Overtime</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Reports</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Edit</a></li>
    </ol>
</div>
@endsection

@section('button')
<a href="{{ route('overtime.reports.show', $overtime) }}" class="btn btn-secondary btn-sm btn-flat">
    <i class="mdi mdi-arrow-left mr-2"></i>Back to Details
</a>
@endsection

@section('content')
@include('includes.flash')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('overtime.reports.update', $overtime) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employee_id">Employee <span class="text-danger">*</span></label>
                                <select class="form-control @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $employee)
                                    <option value="{{ $employee->id_employees }}" {{ old('employee_id', $overtime->emp_id) == $employee->id_employees ? 'selected' : '' }}>
                                        {{ $employee->name }} ({{ $employee->employee_code }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="overtime_date">Overtime Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('overtime_date') is-invalid @enderror"
                                    id="overtime_date" name="overtime_date"
                                    value="{{ old('overtime_date', $overtime->overtime_date) }}" required>
                                @error('overtime_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="duration">Duration <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('duration') is-invalid @enderror"
                                    id="duration" name="duration" step="1"
                                    value="{{ old('duration', $overtime->duration) }}" required>
                                <small class="form-text text-muted">Format: HH:MM:SS (e.g., 08:01:00 for 8 hours 1 minute)</small>
                                @error('duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="reason">Reason <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('reason') is-invalid @enderror"
                                    id="reason" name="reason" rows="4"
                                    placeholder="Enter reason for overtime request" required>{{ old('reason', $overtime->reason) }}</textarea>
                                @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->hasRole(['super_admin', 'admin_sdm']))
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                                    <option value="pending" {{ old('status', $overtime->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ old('status', $overtime->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ old('status', $overtime->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-check mr-2"></i>Update Request
                                </button>
                                <a href="{{ route('overtime.reports.show', $overtime) }}" class="btn btn-secondary ml-2">
                                    <i class="mdi mdi-close mr-2"></i>Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
@endsection