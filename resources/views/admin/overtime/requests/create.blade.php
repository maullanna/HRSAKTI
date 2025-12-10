@extends('layouts.master')

@section('css')
@endsection

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Create Overtime Request</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Overtime</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Create Request</a></li>
    </ol>
</div>
@endsection

@section('button')
<a href="{{ route('overtime.requests') }}" class="btn btn-secondary btn-sm btn-flat">
    <i class="mdi mdi-arrow-left mr-2"></i>Back to Requests
</a>
@endsection

@section('content')
@include('includes.flash')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Display Employee Info -->
                <div class="alert alert-info mb-4">
                    <h5 class="mb-2"><i class="mdi mdi-account mr-2"></i>Employee Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Name:</strong> {{ $employee->name }}</p>
                            <p class="mb-1"><strong>Employee Code:</strong> {{ $employee->employee_code }}</p>
                        </div>
                        <div class="col-md-6">
                            @if($employee->section)
                            <p class="mb-1"><strong>Section:</strong> {{ $employee->section->name }}</p>
                            @endif
                            @if($employee->wadirEmployee)
                            <p class="mb-1"><strong>Wadir:</strong> {{ $employee->wadirEmployee->name }} ({{ $employee->wadirEmployee->position }})</p>
                            @endif
                            @if($employee->sdmEmployee)
                            <p class="mb-0"><strong>SDM/HRD:</strong> {{ $employee->sdmEmployee->name }}</p>
                            @endif
                            @if($employee->directorEmployee)
                            <p class="mb-0"><strong>Direktur:</strong> {{ $employee->directorEmployee->name }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('overtime.store') }}">
                    @csrf

                    <!-- Hidden field untuk employee_id -->
                    <input type="hidden" name="employee_id" value="{{ $employee->id_employees }}">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="overtime_date">Overtime Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('overtime_date') is-invalid @enderror"
                                    id="overtime_date" name="overtime_date"
                                    value="{{ old('overtime_date', date('Y-m-d')) }}" required>
                                @error('overtime_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_time">Start Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                                    id="start_time" name="start_time"
                                    value="{{ old('start_time') }}" required>
                                @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_time">End Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                                    id="end_time" name="end_time"
                                    value="{{ old('end_time') }}" required>
                                @error('end_time')
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
                                    placeholder="Enter reason for overtime request" required>{{ old('reason') }}</textarea>
                                @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-check mr-2"></i>Submit Request
                                </button>
                                <a href="{{ route('overtime.requests') }}" class="btn btn-secondary ml-2">
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