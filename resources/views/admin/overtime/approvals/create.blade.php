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
                <form method="POST" action="{{ route('overtime.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employee_id">Employee <span class="text-danger">*</span></label>
                                <select class="form-control @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id_employees }}" {{ old('employee_id') == $employee->id_employees ? 'selected' : '' }}>
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
                            <div class="form-group">
                                <div class="alert alert-info">
                                    <i class="mdi mdi-information"></i>
                                    <strong>Note:</strong> Overtime request will be submitted for approval. You will be notified once it's reviewed.
                                </div>
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
<script>
    $(document).ready(function() {
        // Auto-calculate duration when times change
        $('#start_time, #end_time').on('change', function() {
            calculateDuration();
        });
        
        function calculateDuration() {
            var startTime = $('#start_time').val();
            var endTime = $('#end_time').val();
            
            if (startTime && endTime) {
                var start = new Date('2000-01-01 ' + startTime);
                var end = new Date('2000-01-01 ' + endTime);
                
                if (end > start) {
                    var diff = end - start;
                    var hours = Math.floor(diff / (1000 * 60 * 60));
                    var minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    
                    // Show duration info
                    if (!$('#duration-info').length) {
                        $('#end_time').after('<div id="duration-info" class="form-text text-muted"></div>');
                    }
                    $('#duration-info').text('Duration: ' + hours + ' hours ' + minutes + ' minutes');
                } else {
                    $('#duration-info').text('End time must be after start time');
                }
            }
        }
        
        // Form validation
        $('form').on('submit', function(e) {
            var startTime = $('#start_time').val();
            var endTime = $('#end_time').val();
            
            if (startTime && endTime) {
                var start = new Date('2000-01-01 ' + startTime);
                var end = new Date('2000-01-01 ' + endTime);
                
                if (end <= start) {
                    e.preventDefault();
                    alert('End time must be after start time!');
                    return false;
                }
            }
        });
    });
</script>
@endsection
