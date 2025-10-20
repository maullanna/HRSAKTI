@extends('layouts.master')

@section('css')
@endsection

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Edit Overtime Request</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Overtime</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Edit Request</a></li>
    </ol>
</div>
@endsection

@section('button')
<a href="{{ route('overtime.show', $overtime) }}" class="btn btn-secondary btn-sm btn-flat">
    <i class="mdi mdi-arrow-left mr-2"></i>Back to Details
</a>
@endsection

@section('content')
@include('includes.flash')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('overtime.update', $overtime) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employee_id">Employee <span class="text-danger">*</span></label>
                                <select class="form-control @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id', $overtime->emp_id) == $employee->id ? 'selected' : '' }}>
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
                                <label for="start_time">Start Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                       id="start_time" name="start_time" 
                                       value="{{ old('start_time', \Carbon\Carbon::parse($overtime->start_time)->format('H:i')) }}" required>
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
                                       value="{{ old('end_time', \Carbon\Carbon::parse($overtime->end_time)->format('H:i')) }}" required>
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
                            <div class="form-group">
                                <div class="alert alert-info">
                                    <i class="mdi mdi-information"></i>
                                    <strong>Note:</strong> Changes to this overtime request will be saved immediately.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-check mr-2"></i>Update Request
                                </button>
                                <a href="{{ route('overtime.show', $overtime) }}" class="btn btn-secondary ml-2">
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
        
        // Calculate duration on page load
        calculateDuration();
        
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
