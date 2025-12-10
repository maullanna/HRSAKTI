@extends('layouts.master')

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Overtime Report</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Overtime</a></li>
        <li class="breadcrumb-item active">Reports</li>
    </ol>
</div>
@endsection

@section('button')
<a href="{{ route('reports.export.overtime') }}?{{ http_build_query(request()->all()) }}" class="btn btn-success btn-sm btn-flat">
    <i class="mdi mdi-download mr-2"></i>Export CSV
</a>
<a href="{{ route('overtime.reports.index') }}" class="btn btn-secondary btn-sm btn-flat">
    <i class="mdi mdi-arrow-left mr-2"></i>Back to Overtime Reports
</a>
@endsection

@section('content')
@include('includes.flash')

@if(isset($isEmployee) && $isEmployee && !isset($canViewAll))
<div class="alert alert-info mb-3">
    <i class="mdi mdi-information mr-2"></i>
    <strong>Info:</strong> Anda hanya dapat melihat report overtime Anda sendiri.
</div>
@endif

<!-- Filter Form -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Filter Overtime Report</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.overtime') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    value="{{ $startDate instanceof \Carbon\Carbon ? $startDate->format('Y-m-d') : $startDate }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="end_date">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                    value="{{ $endDate instanceof \Carbon\Carbon ? $endDate->format('Y-m-d') : $endDate }}">
                            </div>
                        </div>
                        @if(isset($canViewAll) && $canViewAll)
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="employee_id">Employee</label>
                                <select name="employee_id" id="employee_id" class="form-control">
                                    <option value="">All Employees</option>
                                    @foreach($employees as $employee)
                                    <option value="{{ $employee->id_employees }}" {{ $employeeId == $employee->id_employees ? 'selected' : '' }}>
                                        {{ $employee->name }} (ID: {{ $employee->id_employees }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            @else
                            <div class="col-md-5">
                                @endif
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="mdi mdi-magnify mr-1"></i>Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Report Data -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Overtime Report</h5>
                <p class="card-text">
                    Period: {{ ($startDate instanceof \Carbon\Carbon ? $startDate : \Carbon\Carbon::parse($startDate))->format('d M Y') }} - {{ ($endDate instanceof \Carbon\Carbon ? $endDate : \Carbon\Carbon::parse($endDate))->format('d M Y') }}
                    @if($employeeId)
                    | Employee: {{ $employees->where('id_employees', $employeeId)->first()->name ?? 'Unknown' }}
                    @endif
                </p>
            </div>
            <div class="card-body">
                <div class="table-rep-plugin">
                    <div class="table-responsive mb-0" data-pattern="priority-columns">
                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th data-priority="1">Employee</th>
                                    <th data-priority="2">Overtime Date</th>
                                    <th data-priority="3">Time</th>
                                    <th data-priority="4">Duration</th>
                                    <th data-priority="5">Status</th>
                                    <th data-priority="6">Approval Status</th>
                                    <th data-priority="7">Reason</th>
                                    <th data-priority="8">Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($overtimes as $overtime)
                                <tr>
                                    <td>
                                        <strong>{{ $overtime->employee->name ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">ID: {{ $overtime->emp_id }}</small>
                                        @if($overtime->employee && $overtime->employee->position)
                                        <br><small class="text-muted"><i class="mdi mdi-briefcase"></i> {{ $overtime->employee->position }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ \Carbon\Carbon::parse($overtime->overtime_date)->format('d M Y') }}</strong><br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($overtime->overtime_date)->format('l') }}</small>
                                    </td>
                                    <td>
                                        <i class="mdi mdi-clock-start text-primary"></i> <strong>{{ $overtime->start_time ? \Carbon\Carbon::parse($overtime->start_time)->format('H:i') : 'N/A' }}</strong><br>
                                        <i class="mdi mdi-clock-end text-danger"></i> {{ $overtime->end_time ? \Carbon\Carbon::parse($overtime->end_time)->format('H:i') : 'N/A' }}
                                    </td>
                                    <td>
                                        <span class="badge badge-primary" style="font-size: 13px; padding: 6px 12px;">
                                            <i class="mdi mdi-timer"></i> {{ $overtime->duration ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($overtime->status == 'approved')
                                        <span class="badge badge-success">
                                            <i class="mdi mdi-check-circle"></i> Approved
                                        </span>
                                        @elseif($overtime->status == 'pending')
                                        <span class="badge badge-warning">
                                            <i class="mdi mdi-clock-outline"></i> Pending
                                        </span>
                                        @elseif($overtime->status == 'rejected')
                                        <span class="badge badge-danger">
                                            <i class="mdi mdi-close-circle"></i> Rejected
                                        </span>
                                        @else
                                        <span class="badge badge-secondary">{{ ucfirst($overtime->status ?? 'N/A') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            @if($overtime->section_approved)
                                            <span class="badge badge-success mb-1" style="font-size: 11px;">
                                                <i class="mdi mdi-check"></i> Section
                                            </span>
                                            @else
                                            <span class="badge badge-secondary mb-1" style="font-size: 11px;">
                                                <i class="mdi mdi-clock"></i> Section
                                            </span>
                                            @endif
                                            @if($overtime->wadir_approved)
                                            <span class="badge badge-success mb-1" style="font-size: 11px;">
                                                <i class="mdi mdi-check"></i> Wadir
                                            </span>
                                            @else
                                            <span class="badge badge-secondary mb-1" style="font-size: 11px;">
                                                <i class="mdi mdi-clock"></i> Wadir
                                            </span>
                                            @endif
                                            @if($overtime->sdm_approved)
                                            <span class="badge badge-success" style="font-size: 11px;">
                                                <i class="mdi mdi-check"></i> SDM
                                            </span>
                                            @else
                                            <span class="badge badge-secondary" style="font-size: 11px;">
                                                <i class="mdi mdi-clock"></i> SDM
                                            </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($overtime->reason)
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $overtime->reason }}">
                                            {{ Str::limit($overtime->reason, 50) }}
                                        </span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ \Carbon\Carbon::parse($overtime->created_at)->format('d M Y') }}</small><br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($overtime->created_at)->format('H:i:s') }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <div class="py-4">
                                            <i class="mdi mdi-clock-out" style="font-size: 48px; color: #ccc;"></i>
                                            <p class="mt-2 text-muted">No overtime records found for the selected period.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($overtimes->count() > 0)
                <!-- Statistics Cards -->
                <div class="row mt-4">
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card mini-stat bg-primary text-white">
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="float-left mini-stat-img mr-3">
                                        <i class="mdi mdi-file-document" style="font-size: 24px;"></i>
                                    </div>
                                    <h5 class="font-16 text-uppercase mt-0 text-white-50">Total Records</h5>
                                    <h3 class="font-500 text-white mb-0">{{ $totalRecords ?? 0 }}</h3>
                                </div>
                                <p class="text-white-50 mb-0">Overtime entries</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card mini-stat bg-success text-white">
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="float-left mini-stat-img mr-3">
                                        <i class="mdi mdi-clock" style="font-size: 24px;"></i>
                                    </div>
                                    <h5 class="font-16 text-uppercase mt-0 text-white-50">Total Hours</h5>
                                    <h3 class="font-500 text-white mb-0">{{ number_format($totalHours ?? 0, 2) }}</h3>
                                </div>
                                <p class="text-white-50 mb-0">Hours worked</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card mini-stat bg-info text-white">
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="float-left mini-stat-img mr-3">
                                        <i class="mdi mdi-account-multiple" style="font-size: 24px;"></i>
                                    </div>
                                    <h5 class="font-16 text-uppercase mt-0 text-white-50">Total Employees</h5>
                                    <h3 class="font-500 text-white mb-0">{{ $totalEmployees ?? 0 }}</h3>
                                </div>
                                <p class="text-white-50 mb-0">Unique employees</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card mini-stat bg-warning text-white">
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="float-left mini-stat-img mr-3">
                                        <i class="mdi mdi-chart-line" style="font-size: 24px;"></i>
                                    </div>
                                    <h5 class="font-16 text-uppercase mt-0 text-white-50">Average Duration</h5>
                                    <h3 class="font-500 text-white mb-0">{{ number_format($averageHours ?? 0, 2) }}</h3>
                                </div>
                                <p class="text-white-50 mb-0">Hours per record</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Breakdown -->
                <div class="row mt-3">
                    <div class="col-xl-4 col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="mdi mdi-chart-pie mr-2"></i>Status Breakdown
                                </h5>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span><i class="mdi mdi-clock-outline text-warning mr-2"></i>Pending</span>
                                    <span class="badge badge-warning badge-pill">{{ $pendingCount ?? 0 }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span><i class="mdi mdi-check-circle text-success mr-2"></i>Approved</span>
                                    <span class="badge badge-success badge-pill">{{ $approvedCount ?? 0 }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><i class="mdi mdi-close-circle text-danger mr-2"></i>Rejected</span>
                                    <span class="badge badge-danger badge-pill">{{ $rejectedCount ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="mdi mdi-check-all mr-2"></i>Approval Progress
                                </h5>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span><i class="mdi mdi-account-check text-primary mr-2"></i>Section Approved</span>
                                    <span class="badge badge-primary badge-pill">{{ $sectionApprovedCount ?? 0 }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span><i class="mdi mdi-account-check text-info mr-2"></i>Wadir Approved</span>
                                    <span class="badge badge-info badge-pill">{{ $wadirApprovedCount ?? 0 }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span><i class="mdi mdi-account-check text-success mr-2"></i>SDM Approved</span>
                                    <span class="badge badge-success badge-pill">{{ $sdmApprovedCount ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Card -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="mdi mdi-information mr-2"></i>Report Summary
                                </h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="mb-2">
                                            <strong><i class="mdi mdi-calendar-range mr-2"></i>Date Range:</strong><br>
                                            <span class="ml-4">{{ ($startDate instanceof \Carbon\Carbon ? $startDate : \Carbon\Carbon::parse($startDate))->format('d M Y') }} - {{ ($endDate instanceof \Carbon\Carbon ? $endDate : \Carbon\Carbon::parse($endDate))->format('d M Y') }}</span>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-2">
                                            <strong><i class="mdi mdi-file-document-multiple mr-2"></i>Total Records:</strong><br>
                                            <span class="ml-4">{{ $totalRecords ?? 0 }} overtime entries</span>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-2">
                                            <strong><i class="mdi mdi-account-group mr-2"></i>Total Employees:</strong><br>
                                            <span class="ml-4">{{ $totalEmployees ?? 0 }} unique employees</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#datatable-buttons').DataTable({
            destroy: true,
            responsive: true,
            pageLength: 25,
            order: [
                [1, 'desc']
            ]
        });
    });
</script>
@endsection