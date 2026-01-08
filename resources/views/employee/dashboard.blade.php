@extends('layouts.master')

@section('css')
<!--Chartist Chart CSS -->
<link rel="stylesheet" href="{{ URL::asset('plugins/chartist/css/chartist.min.css') }}">
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
    }

    .dashboard-header h3 {
        margin: 0;
        font-weight: 600;
    }

    .dashboard-header p {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        height: 100%;
    }

    .stat-card:hover {
        border-color: #667eea;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
        transform: translateY(-2px);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 1rem;
    }

    .stat-icon.primary {
        background: #e3f2fd;
        color: #1976d2;
    }

    .stat-icon.success {
        background: #e8f5e9;
        color: #388e3c;
    }

    .stat-icon.warning {
        background: #fff3e0;
        color: #f57c00;
    }

    .stat-icon.danger {
        background: #ffebee;
        color: #d32f2f;
    }

    .stat-icon.info {
        background: #e0f2f1;
        color: #00796b;
    }

    .stat-icon.purple {
        background: #f3e5f5;
        color: #7b1fa2;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0.5rem 0;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-subtitle {
        font-size: 0.75rem;
        color: #adb5bd;
        margin-top: 0.25rem;
    }

    /* Samakan tinggi semua box statistik */
    .card.mini-stat {
        height: 100% !important;
        display: flex !important;
        flex-direction: column !important;
    }

    .card.mini-stat .card-body {
        flex: 1 !important;
        display: flex !important;
        flex-direction: column !important;
    }

    .card.mini-stat .mb-4 {
        flex: 1 !important;
        min-height: 80px !important;
    }

    .today-status-card {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        border: 1px solid #e9ecef;
        margin-bottom: 2rem;
    }

    .status-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .status-badge.present {
        background: #e8f5e9;
        color: #388e3c;
    }

    .status-badge.late {
        background: #fff3e0;
        color: #f57c00;
    }

    .status-badge.absent {
        background: #ffebee;
        color: #d32f2f;
    }

    .quick-actions {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        border: 1px solid #e9ecef;
        margin-bottom: 2rem;
    }

    .action-btn {
        display: block;
        padding: 1rem;
        text-align: center;
        border-radius: 8px;
        border: 1px solid #e9ecef;
        background: white;
        color: #495057;
        text-decoration: none;
        transition: all 0.3s ease;
        height: 100%;
    }

    .action-btn:hover {
        border-color: #667eea;
        color: #667eea;
        background: #f8f9ff;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
    }

    .action-btn i {
        font-size: 1.5rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    .activity-card {
        background: white;
        border-radius: 8px;
        border: 1px solid #e9ecef;
        height: 100%;
    }

    .activity-card .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 1rem 1.5rem;
        font-weight: 600;
        color: #495057;
    }

    .activity-item {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f1f3f5;
        transition: background 0.2s ease;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-item:hover {
        background: #f8f9fa;
    }

    .activity-date {
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.875rem;
    }

    .activity-detail {
        font-size: 0.8125rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }

    .badge-status {
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .badge-status.approved {
        background: #e8f5e9;
        color: #388e3c;
    }

    .badge-status.pending {
        background: #fff3e0;
        color: #f57c00;
    }

    .badge-status.rejected {
        background: #ffebee;
        color: #d32f2f;
    }

    .badge-status.ontime {
        background: #e8f5e9;
        color: #388e3c;
    }

    .badge-status.late {
        background: #fff3e0;
        color: #f57c00;
    }

    .empty-state {
        padding: 2rem;
        text-align: center;
        color: #adb5bd;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    /* Mobile Responsive Styles */
    @media (max-width: 768px) {

        /* Dashboard Header - dengan spacing yang lebih baik */
        .dashboard-header {
            padding: 1.25rem !important;
            margin-bottom: 1rem !important;
            margin-top: 0.5rem !important;
        }

        .dashboard-header h3 {
            font-size: 1.2rem !important;
        }

        .dashboard-header p {
            font-size: 0.875rem !important;
        }

        /* Stat Cards - dengan spacing yang lebih baik */
        .stat-card {
            padding: 1rem !important;
            margin-bottom: 1rem !important;
            margin-top: 0.5rem !important;
        }

        /* Setiap box/card memiliki margin-top 0.5rem (spacing 2) */
        .card.mini-stat,
        .stat-card,
        .today-status-card,
        .quick-actions,
        .activity-card,
        .dashboard-header,
        .card {
            margin-top: 0.5rem !important;
        }

        .stat-icon {
            width: 42px !important;
            height: 42px !important;
            font-size: 20px !important;
        }

        .stat-value {
            font-size: 1.4rem !important;
        }

        .stat-label {
            font-size: 0.8rem !important;
        }

        /* Mini Stat Cards - dengan spacing yang lebih baik */
        .card.mini-stat {
            margin-bottom: 1rem !important;
            margin-top: 0.5rem !important;
        }

        .card.mini-stat .card-body {
            padding: 1rem !important;
        }

        .card.mini-stat .mb-4 {
            margin-bottom: 0.75rem !important;
        }

        .card.mini-stat h5 {
            font-size: 0.8rem !important;
            line-height: 1.3 !important;
        }

        .card.mini-stat h4 {
            font-size: 1.4rem !important;
        }

        .card.mini-stat p {
            font-size: 0.8rem !important;
        }

        .card.mini-stat i {
            font-size: 20px !important;
        }

        /* Mini stat img container - posisi icon diperbesar dan disesuaikan */
        .mini-stat-img {
            width: 42px !important;
            height: 42px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            line-height: 1 !important;
            flex-shrink: 0 !important;
        }

        .mini-stat-img i {
            font-size: 20px !important;
            line-height: 1 !important;
            vertical-align: middle !important;
            display: inline-block !important;
        }

        /* Pastikan float-left tidak mengganggu alignment */
        .float-left.mini-stat-img {
            float: left !important;
            margin-right: 0.75rem !important;
        }

        /* Today Status Card - dengan spacing yang lebih baik */
        .today-status-card {
            padding: 1rem !important;
            margin-bottom: 1rem !important;
            margin-top: 0.5rem !important;
        }

        .today-status-card h5 {
            font-size: 1rem !important;
        }

        .status-badge {
            font-size: 0.8rem !important;
            padding: 0.5rem 1rem !important;
        }

        /* Quick Actions - dengan spacing yang lebih baik */
        .quick-actions {
            padding: 1rem !important;
            margin-bottom: 1rem !important;
            margin-top: 0.5rem !important;
        }

        .action-btn {
            padding: 0.75rem !important;
            margin-bottom: 0.75rem !important;
            font-size: 0.875rem !important;
        }

        .action-btn i {
            font-size: 1.2rem !important;
        }

        /* Card Body - dengan spacing yang lebih baik */
        .card-body {
            padding: 1rem !important;
        }

        /* Chart containers - dengan spacing yang lebih baik */
        .chart-container,
        .ct-chart,
        #employee-training-chart {
            height: 220px !important;
            max-height: 220px !important;
        }

        .ct-chart .ct-label {
            font-size: 0.8rem !important;
        }

        .ct-chart .ct-line {
            stroke-width: 1.5px !important;
        }

        .ct-chart .ct-point {
            stroke-width: 2px !important;
            stroke: transparent !important;
        }

        /* Table - lebih compact */
        .table-responsive {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
        }

        table {
            font-size: 0.8rem !important;
        }

        table th,
        table td {
            padding: 0.5rem !important;
        }

        /* Activity Card - dengan spacing yang lebih baik */
        .activity-card {
            margin-bottom: 1rem !important;
            margin-top: 0.5rem !important;
        }

        .activity-card .card-header {
            padding: 0.875rem !important;
            font-size: 0.95rem !important;
        }

        /* Typography - diperbesar sedikit */
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-size: 90% !important;
        }

        p {
            font-size: 0.875rem !important;
        }

        /* Spacing antar card - ditambahkan */
        .row.mb-4 {
            margin-bottom: 1rem !important;
            margin-top: 0.5rem !important;
        }

        /* Card margin - dengan spacing yang lebih baik */
        .card {
            margin-bottom: 1rem !important;
            margin-top: 0.5rem !important;
        }

        /* Kolom dalam row juga memiliki margin-top */
        .row>[class*="col-"] {
            margin-top: 0.5rem !important;
        }

        /* Mini stat img - diperbesar dan posisi disesuaikan */
        .mini-stat-img {
            width: 42px !important;
            height: 42px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .mini-stat-img i {
            font-size: 20px !important;
            line-height: 1 !important;
        }

        /* Chart section - dengan spacing yang lebih baik */
        .col-xl-8,
        .col-xl-4 {
            margin-bottom: 1rem !important;
            margin-top: 0.5rem !important;
        }

        /* Card header title - diperbesar sedikit */
        .card h4.header-title {
            font-size: 1rem !important;
            margin-bottom: 1rem !important;
            margin-top: 0.5rem !important;
        }

        /* Training section - dengan spacing yang lebih baik */
        .row.mt-4 {
            margin-top: 1rem !important;
            margin-bottom: 0.5rem !important;
        }
    }

    @media (max-width: 480px) {

        /* Dashboard Header - dengan spacing yang lebih baik */
        .dashboard-header {
            padding: 1rem !important;
            margin-bottom: 0.75rem !important;
            margin-top: 0.5rem !important;
        }

        .dashboard-header h3 {
            font-size: 1.1rem !important;
        }

        .dashboard-header p {
            font-size: 0.8rem !important;
        }

        /* Stat Cards - dengan spacing yang lebih baik */
        .stat-card {
            padding: 0.875rem !important;
            margin-bottom: 0.75rem !important;
            margin-top: 0.5rem !important;
        }

        .stat-value {
            font-size: 1.3rem !important;
        }

        .stat-icon {
            width: 38px !important;
            height: 38px !important;
            font-size: 18px !important;
        }

        /* Mini Stat Cards - dengan spacing yang lebih baik */
        .card.mini-stat .card-body {
            padding: 0.875rem !important;
        }

        .card.mini-stat h5 {
            font-size: 0.75rem !important;
        }

        .card.mini-stat h4 {
            font-size: 1.3rem !important;
        }

        .card.mini-stat p {
            font-size: 0.75rem !important;
        }

        .card.mini-stat i {
            font-size: 18px !important;
        }

        /* Mini stat img - diperbesar dan posisi disesuaikan */
        .mini-stat-img {
            width: 38px !important;
            height: 38px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            line-height: 1 !important;
        }

        .mini-stat-img i {
            font-size: 18px !important;
            line-height: 1 !important;
            vertical-align: middle !important;
        }

        /* Today Status Card - dengan spacing yang lebih baik */
        .today-status-card {
            padding: 0.875rem !important;
            margin-bottom: 0.75rem !important;
            margin-top: 0.5rem !important;
        }

        .today-status-card h5 {
            font-size: 0.95rem !important;
        }

        .status-badge {
            font-size: 0.75rem !important;
            padding: 0.4rem 0.8rem !important;
        }

        /* Action Buttons - dengan spacing yang lebih baik */
        .action-btn {
            padding: 0.65rem !important;
            margin-bottom: 0.65rem !important;
            font-size: 0.8rem !important;
        }

        .action-btn i {
            font-size: 1.1rem !important;
        }

        /* Chart - dengan spacing yang lebih baik */
        .ct-chart,
        #employee-training-chart {
            height: 200px !important;
            max-height: 200px !important;
        }

        /* Table - dengan spacing yang lebih baik */
        table {
            font-size: 0.75rem !important;
        }

        table th,
        table td {
            padding: 0.45rem !important;
        }

        /* Card spacing - dengan spacing yang lebih baik */
        .row.mb-4 {
            margin-bottom: 0.75rem !important;
            margin-top: 0.5rem !important;
        }

        .card {
            margin-bottom: 0.75rem !important;
            margin-top: 0.5rem !important;
        }

        /* Setiap box/card memiliki margin-top 2 di extra small */
        .card,
        .card.mini-stat,
        .stat-card,
        .today-status-card,
        .quick-actions,
        .activity-card,
        .dashboard-header {
            margin-top: 0.5rem !important;
        }

        /* Kolom dalam row juga memiliki margin-top */
        .row>[class*="col-"] {
            margin-top: 0.5rem !important;
        }

        /* Typography - diperbesar sedikit */
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-size: 85% !important;
        }

        p {
            font-size: 0.8rem !important;
        }
    }
</style>
@endsection

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Dashboard</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
</div>
@endsection

@section('button')
<div class="col-sm-6">
    <div class="d-flex justify-content-end align-items-center h-100" style="flex-wrap: nowrap; min-width: 0;">
        <span class="text-muted" style="font-size: 1.1rem; white-space: nowrap; flex-shrink: 0;">
            {{ __('global.dashboard.welcome') }},
        </span>
        <span class="ml-1" style="font-weight: 600; white-space: nowrap; display: inline-block; flex-shrink: 0;">
            {{ $employee->name }}
        </span>
    </div>
</div>
@endsection

@section('content')
<!-- TEST: Content section is loading -->
<div class="alert alert-success">
    <strong>✓ Content Section Loaded Successfully!</strong>
</div>

@include('includes.flash')

@php
// Debug: Check if variables are set
$debugMode = true; // Set to true to see debug info
@endphp



<!-- Dashboard Header -->
@if(isset($employee))
<div class="dashboard-header">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h3>{{ $employee->name ?? 'N/A' }}</h3>
            <p><i class="mdi mdi-briefcase mr-2"></i>{{ $employee->position ?? 'N/A' }} | <i class="mdi mdi-identifier mr-2"></i>ID: {{ $employee->id_employees ?? 'N/A' }}</p>
        </div>
        <div class="col-md-4 text-right">
            <p class="mb-0"><i class="mdi mdi-email mr-2"></i>{{ $employee->email ?? 'N/A' }}</p>
        </div>
    </div>
</div>
@else
<div class="alert alert-danger">
    <strong>Error:</strong> Employee data not found!
</div>
@endif

@php
// Ensure position is set, default to empty string if not
$position = $position ?? '';
$isSection = !empty($position) && strpos($position, 'Section ') === 0;
$isWadir = !empty($position) && in_array($position, ['Wadir 1', 'Wadir 2']);
$isSdm = !empty($position) && $position === 'SDM/HRD';
$isRegularEmployee = !$isSection && !$isWadir && !$isSdm;
@endphp

@if($isRegularEmployee)
<!-- Today's Status - Only for regular employees, Magang, and PKL -->
<div class="today-status-card">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h5 class="mb-2">{{ __('global.dashboard.todays_attendance_status') }}</h5>
            @if(isset($todayAttendance))
            <p class="mb-1">
                <span class="status-badge {{ $todayAttendance->status == 1 ? 'present' : 'late' }}">
                    <i class="mdi mdi-{{ $todayAttendance->status == 1 ? 'check-circle' : 'clock-alert' }} mr-1"></i>
                    {{ $todayAttendance->status == 1 ? __('global.dashboard.present_on_time') : __('global.dashboard.present_late') }}
                </span>
            </p>
            <p class="mb-0 text-muted" style="font-size: 0.875rem;">
                {{ __('global.dashboard.check_in') }}: {{ \Carbon\Carbon::parse($todayAttendance->attendance_time)->format('H:i:s') }}
            </p>
            @else
            <p class="mb-1">
                <span class="status-badge absent">
                    <i class="mdi mdi-close-circle mr-1"></i>
                    {{ __('global.dashboard.not_checked_in') }}
                </span>
            </p>
            <p class="mb-0 text-muted" style="font-size: 0.875rem;">{{ __('global.dashboard.please_check_in') }}</p>
            @endif
        </div>
        <div class="col-md-4 text-right">
            <p class="mb-0 text-muted" style="font-size: 0.875rem;">{{ now()->format('l, F d, Y') }}</p>
        </div>
    </div>
</div>
@endif

@if($isRegularEmployee)
<!-- Statistics - Only for regular employees, Magang, and PKL -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 col-6">
        <div class="card mini-stat bg-primary text-white">
            <div class="card-body">
                <div class="mb-4">
                    <div class="float-left mini-stat-img mr-4">
                        <i class="mdi mdi-calendar-check" style="font-size: 20px"></i>
                    </div>
                    <h5 class="font-16 text-uppercase mt-0 text-white" style="opacity: 0.8;">{{ __('global.dashboard.total_attendance') }} <br> </h5>
                    <h4 class="font-500 text-white" style="opacity: 0.9;">{{ $totalAttendance ?? 0 }}</h4>
                </div>
                <div class="pt-2">
                    <p class="text-white mb-0" style="opacity: 0.7;">{{ __('global.dashboard.this_month') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-6">
        <div class="card mini-stat bg-primary text-white">
            <div class="card-body">
                <div class="mb-4">
                    <div class="float-left mini-stat-img mr-4">
                        <i class="mdi mdi-check-circle" style="font-size: 20px"></i>
                    </div>
                    <h5 class="font-16 text-uppercase mt-0 text-white" style="opacity: 0.8;">{{ __('global.dashboard.on_time') }} <br> </h5>
                    <h4 class="font-500 text-white" style="opacity: 0.9;">{{ $onTimeAttendance ?? 0 }}</h4>
                </div>
                <div class="pt-2">
                    <p class="text-white mb-0" style="opacity: 0.7;">{{ __('global.dashboard.this_month') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-6">
        <div class="card mini-stat bg-primary text-white">
            <div class="card-body">
                <div class="mb-4">
                    <div class="float-left mini-stat-img mr-4">
                        <i class="mdi mdi-clock-alert" style="font-size: 20px"></i>
                    </div>
                    <h5 class="font-16 text-uppercase mt-0 text-white" style="opacity: 0.8;">{{ __('global.dashboard.late_arrivals') }} <br> </h5>
                    <h4 class="font-500 text-white" style="opacity: 0.9;">{{ $lateAttendance ?? 0 }}</h4>
                </div>
                <div class="pt-2">
                    <p class="text-white mb-0" style="opacity: 0.7;">{{ __('global.dashboard.this_month') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-6">
        <div class="card mini-stat bg-primary text-white">
            <div class="card-body">
                <div class="mb-4">
                    <div class="float-left mini-stat-img mr-4">
                        <i class="mdi mdi-calendar-remove" style="font-size: 20px"></i>
                    </div>
                    <h5 class="font-16 text-uppercase mt-0 text-white" style="opacity: 0.8;">{{ __('global.dashboard.leave_requests') }} <br> </h5>
                    <h4 class="font-500 text-white" style="opacity: 0.9;">{{ $totalLeaves ?? 0 }}</h4>
                </div>
                <div class="pt-2">
                    <p class="text-white mb-0" style="opacity: 0.7;">{{ __('global.dashboard.this_month') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-xl-3 col-md-6 col-6">
        <div class="card mini-stat bg-primary text-white">
            <div class="card-body">
                <div class="mb-4">
                    <div class="float-left mini-stat-img mr-4">
                        <i class="mdi mdi-check-circle-outline" style="font-size: 20px"></i>
                    </div>
                    <h5 class="font-16 text-uppercase mt-0 text-white" style="opacity: 0.8;">{{ __('global.dashboard.approved_leaves') }} <br> </h5>
                    <h4 class="font-500 text-white" style="opacity: 0.9;">{{ $approvedLeaves ?? 0 }}</h4>
                </div>
                <div class="pt-2">
                    <p class="text-white mb-0" style="opacity: 0.7;">{{ __('global.dashboard.this_month') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-6">
        <div class="card mini-stat bg-primary text-white">
            <div class="card-body">
                <div class="mb-4">
                    <div class="float-left mini-stat-img mr-4">
                        <i class="mdi mdi-clock-outline" style="font-size: 20px"></i>
                    </div>
                    <h5 class="font-16 text-uppercase mt-0 text-white" style="opacity: 0.8;">{{ __('global.dashboard.pending_leaves') }} <br> </h5>
                    <h4 class="font-500 text-white" style="opacity: 0.9;">{{ $pendingLeaves ?? 0 }}</h4>
                </div>
                <div class="pt-2">
                    <p class="text-white mb-0" style="opacity: 0.7;">{{ __('global.dashboard.awaiting_approval') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-6">
        <div class="card mini-stat bg-primary text-white">
            <div class="card-body">
                <div class="mb-4">
                    <div class="float-left mini-stat-img mr-4">
                        <i class="mdi mdi-clock" style="font-size: 20px"></i>
                    </div>
                    <h5 class="font-16 text-uppercase mt-0 text-white" style="opacity: 0.8;">+ {{ __('global.dashboard.overtime_hours') }} <br> </h5>
                    <h4 class="font-500 text-white" style="opacity: 0.9;">{{ $totalOvertimes ?? 0 }}</h4>
                </div>
                <div class="pt-2">
                    <p class="text-white mb-0" style="opacity: 0.7;">{{ __('global.dashboard.this_month') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-6">
        <div class="card mini-stat bg-primary text-white">
            <div class="card-body">
                <div class="mb-4">
                    <div class="float-left mini-stat-img mr-4">
                        <i class="mdi mdi-timer-sand" style="font-size: 20px"></i>
                    </div>
                    <h5 class="font-16 text-uppercase mt-0 text-white" style="opacity: 0.8;">{{ __('global.dashboard.pending_overtime') }} <br> </h5>
                    <h4 class="font-500 text-white" style="opacity: 0.9;">{{ $pendingOvertimes ?? 0 }}</h4>
                </div>
                <div class="pt-2">
                    <p class="text-white mb-0" style="opacity: 0.7;">{{ __('global.dashboard.awaiting_approval') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Today's Statistics - On Time Percentage, On Time Today, Late Today -->
<div class="row mb-4">
    <div class="col-xl-4 col-md-6 col-6">
        <div class="card mini-stat bg-success text-white">
            <div class="card-body">
                <div class="mb-4">
                    <div class="float-left mini-stat-img mr-4">
                        <i class="mdi mdi-chart-line" style="font-size: 20px"></i>
                    </div>
                    <h5 class="font-16 text-uppercase mt-0 text-white" style="opacity: 0.8;">On Time <br> Percentage</h5>
                    <h4 class="font-500 text-white" style="opacity: 0.9;">{{ $onTimePercentage ?? 0 }}%</h4>
                </div>
                <div class="pt-2">
                    <p class="text-white mb-0" style="opacity: 0.7;">More info</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 col-6">
        <div class="card mini-stat bg-success text-white">
            <div class="card-body">
                <div class="mb-4">
                    <div class="float-left mini-stat-img mr-4">
                        <i class="mdi mdi-check-circle" style="font-size: 20px"></i>
                    </div>
                    <h5 class="font-16 text-uppercase mt-0 text-white" style="opacity: 0.8;">On Time <br> Today</h5>
                    <h4 class="font-500 text-white" style="opacity: 0.9;">{{ $onTimeToday ?? 0 }}</h4>
                </div>
                <div class="pt-2">
                    <p class="text-white mb-0" style="opacity: 0.7;">More info</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 col-6">
        <div class="card mini-stat bg-danger text-white">
            <div class="card-body">
                <div class="mb-4">
                    <div class="float-left mini-stat-img mr-4">
                        <i class="mdi mdi-clock-alert" style="font-size: 20px"></i>
                    </div>
                    <h5 class="font-16 text-uppercase mt-0 text-white" style="opacity: 0.8;">Late <br> Today</h5>
                    <h4 class="font-500 text-white" style="opacity: 0.9;">{{ $lateToday ?? 0 }}</h4>
                </div>
                <div class="pt-2">
                    <p class="text-white mb-0" style="opacity: 0.7;">More info</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@php
// Reuse the same variables defined above
$hasApprovalRole = $isSection || $isWadir || $isSdm;
@endphp

@if($hasApprovalRole)
<!-- Approval Statistics for Section, Wadir, SDM/HRD -->
<div class="row mb-4">
    <div class="col-12">
        <h5 class="mb-3" style="color: #333; font-weight: 600;">
            <i class="mdi mdi-check-circle-outline mr-2"></i>Approval Dashboard
        </h5>
    </div>
    <div class="col-xl-3 col-md-6 col-6">
        <div class="card mini-stat bg-warning text-white">
            <div class="card-body">
                <div class="mb-4">
                    <div class="float-left mini-stat-img mr-4">
                        <i class="mdi mdi-calendar-clock" style="font-size: 20px"></i>
                    </div>
                    <h5 class="font-16 text-uppercase mt-0 text-white" style="opacity: 0.9;">Pending Leave <br> Approvals</h5>
                    <h4 class="font-500 text-white" style="opacity: 0.95;">{{ $pendingLeaveApprovals ?? 0 }}</h4>
                </div>
                <div class="pt-2">
                    <p class="text-white mb-0" style="opacity: 0.8;">Awaiting Your Approval</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-6">
        <div class="card mini-stat bg-warning text-white">
            <div class="card-body">
                <div class="mb-4">
                    <div class="float-left mini-stat-img mr-4">
                        <i class="mdi mdi-clock-alert" style="font-size: 20px"></i>
                    </div>
                    <h5 class="font-16 text-uppercase mt-0 text-white" style="opacity: 0.9;">Pending Overtime <br> Approvals</h5>
                    <h4 class="font-500 text-white" style="opacity: 0.95;">{{ $pendingOvertimeApprovals ?? 0 }}</h4>
                </div>
                <div class="pt-2">
                    <p class="text-white mb-0" style="opacity: 0.8;">Awaiting Your Approval</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-6">
        <div class="card mini-stat bg-info text-white">
            <div class="card-body">
                <div class="mb-4">
                    <div class="float-left mini-stat-img mr-4">
                        <i class="mdi mdi-account-check" style="font-size: 20px"></i>
                    </div>
                    <h5 class="font-16 text-uppercase mt-0 text-white" style="opacity: 0.9;">Your Role <br> </h5>
                    <h4 class="font-500 text-white" style="opacity: 0.95; font-size: 18px;">
                        @if($isSection)
                        Section Head
                        @elseif($isWadir)
                        Wadir
                        @elseif($isSdm)
                        SDM/HRD
                        @endif
                    </h4>
                </div>
                <div class="pt-2">
                    <a href="/overtime/approvals" class="text-white" style="opacity: 0.9; text-decoration: none;">
                        <i class="mdi mdi-arrow-right mr-1"></i>View Approvals
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-6">
        <div class="card mini-stat bg-success text-white">
            <div class="card-body">
                <div class="mb-4">
                    <div class="float-left mini-stat-img mr-4">
                        <i class="mdi mdi-file-document-check" style="font-size: 20px"></i>
                    </div>
                    <h5 class="font-16 text-uppercase mt-0 text-white" style="opacity: 0.9;">Total Pending <br> </h5>
                    <h4 class="font-500 text-white" style="opacity: 0.95;">{{ ($pendingLeaveApprovals ?? 0) + ($pendingOvertimeApprovals ?? 0) }}</h4>
                </div>
                <div class="pt-2">
                    <p class="text-white mb-0" style="opacity: 0.8;">All Approvals</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@if($isRegularEmployee)
<!-- Recent Activities - Only for regular employees, Magang, and PKL -->
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="activity-card">
            <div class="card-header">
                <i class="mdi mdi-calendar-check mr-2"></i>{{ __('global.dashboard.recent_attendance') }}
            </div>
            <div class="card-body p-0">
                @if(isset($recentAttendances) && $recentAttendances->count() > 0)
                @foreach($recentAttendances as $attendance)
                <div class="activity-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="activity-date">
                                {{ \Carbon\Carbon::parse($attendance->attendance_date)->format('M d, Y') }}
                            </div>
                            <div class="activity-detail">
                                {{ \Carbon\Carbon::parse($attendance->attendance_time)->format('H:i:s') }}
                            </div>
                        </div>
                        <span class="badge-status {{ $attendance->status == 1 ? 'ontime' : 'late' }}">
                            {{ $attendance->status == 1 ? __('global.dashboard.on_time') : __('global.dashboard.late') }}
                        </span>
                    </div>
                </div>
                @endforeach
                @else
                <div class="empty-state">
                    <i class="mdi mdi-calendar-remove"></i>
                    <p>{{ __('global.dashboard.no_attendance_records') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="activity-card">
            <div class="card-header">
                <i class="mdi mdi-calendar-remove mr-2"></i>{{ __('global.dashboard.recent_leaves') }}
            </div>
            <div class="card-body p-0">
                @if(isset($recentLeaves) && $recentLeaves->count() > 0)
                @foreach($recentLeaves as $leave)
                <div class="activity-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="activity-date">
                                {{ \Carbon\Carbon::parse($leave->leave_date)->format('M d, Y') }}
                            </div>
                            <div class="activity-detail">
                                {{ \Illuminate\Support\Str::limit($leave->reason ?? 'No reason', 30) }}
                            </div>
                        </div>
                        <span class="badge-status {{ $leave->status }}">
                            {{ ucfirst($leave->status) }}
                        </span>
                    </div>
                </div>
                @endforeach
                @else
                <div class="empty-state">
                    <i class="mdi mdi-calendar-remove"></i>
                    <p>{{ __('global.dashboard.no_leave_requests') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="activity-card">
            <div class="card-header">
                <i class="mdi mdi-clock-plus mr-2"></i>{{ __('global.dashboard.recent_overtime') }}
            </div>
            <div class="card-body p-0">
                @if(isset($recentOvertimes) && $recentOvertimes->count() > 0)
                @foreach($recentOvertimes as $overtime)
                <div class="activity-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="activity-date">
                                {{ \Carbon\Carbon::parse($overtime->overtime_date)->format('M d, Y') }}
                            </div>
                            <div class="activity-detail">
                                {{ $overtime->duration ?? 'N/A' }} hours
                            </div>
                        </div>
                        <span class="badge-status {{ $overtime->status }}">
                            {{ ucfirst($overtime->status) }}
                        </span>
                    </div>
                </div>
                @endforeach
                @else
                <div class="empty-state">
                    <i class="mdi mdi-clock-remove"></i>
                    <p>{{ __('global.dashboard.no_overtime_requests') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Training Section -->
<div class="row mt-4">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title mb-4">{{ __('global.dashboard.my_training_overview') }}</h4>
                <div id="employee-training-chart" class="ct-chart earning ct-golden-section"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title mb-4">{{ __('global.dashboard.my_training_statistics') }}</h4>
                <div class="wid-peity mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="text-muted mb-0">{{ __('global.dashboard.total_trainings') }}</p>
                        <h5 class="mb-0 text-primary">{{ $totalEmployeeTrainings ?? 0 }}</h5>
                    </div>
                </div>
                <div class="wid-peity mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="text-muted mb-0">{{ __('global.dashboard.completed') }}</p>
                        <h5 class="mb-0 text-success">{{ $completedEmployeeTrainings ?? 0 }}</h5>
                    </div>
                </div>
                <div class="wid-peity">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="text-muted mb-0">{{ __('global.dashboard.ongoing') }}</p>
                        <h5 class="mb-0 text-warning">{{ $ongoingEmployeeTrainings ?? 0 }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end row -->

<!-- Training Table Section -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mt-0 header-title mb-4">{{ __('global.dashboard.my_training_this_month') }}</h4>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>{{ __('global.dashboard.employee') }}</th>
                                <th>{{ __('global.dashboard.title') }}</th>
                                <th>{{ __('global.dashboard.category') }}</th>
                                <th>{{ __('global.dashboard.start_date') }}</th>
                                <th>{{ __('global.dashboard.end_date') }}</th>
                                <th>{{ __('global.dashboard.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employeeTrainingsThisMonth ?? [] as $training)
                            <tr>
                                <td>{{ $training->employee->name ?? 'N/A' }}</td>
                                <td>{{ $training->title }}</td>
                                <td><span class="badge badge-info">{{ $training->category }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($training->start_date)->format('M d, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($training->end_date)->format('M d, Y') }}</td>
                                <td>
                                    @if($training->status == 'completed')
                                    <span class="badge badge-success">{{ __('global.dashboard.completed') }}</span>
                                    @elseif($training->status == 'ongoing')
                                    <span class="badge badge-warning">{{ __('global.dashboard.ongoing') }}</span>
                                    @elseif($training->status == 'planned')
                                    <span class="badge badge-info">Planned</span>
                                    @else
                                    <span class="badge badge-danger">Cancelled</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">{{ __('global.dashboard.no_training_records') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end row -->

@endsection

@section('script')
<!--Chartist Chart-->
<script src="{{ URL::asset('plugins/chartist/js/chartist.min.js') }}"></script>
<script src="{{ URL::asset('plugins/chartist/js/chartist-plugin-tooltip.min.js') }}"></script>
<script>
    // Training chart data for employee
    window.employeeTrainingChartLabelsData = <?php echo json_encode($employeeTrainingMonthlyLabels ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    window.employeeTrainingChartDataData = <?php echo json_encode($employeeTrainingMonthlyData ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

    $(document).ready(function() {
        // Employee Training chart
        var employeeTrainingChartLabels = window.employeeTrainingChartLabelsData || [];
        var employeeTrainingChartData = window.employeeTrainingChartDataData || [];

        // Ensure we have data for the chart
        if (employeeTrainingChartLabels.length === 0) {
            // Generate default labels for last 12 months if no data
            for (var i = 11; i >= 0; i--) {
                var date = new Date();
                date.setMonth(date.getMonth() - i);
                employeeTrainingChartLabels.push(date.toLocaleDateString('en-US', {
                    month: 'short',
                    year: 'numeric'
                }));
                employeeTrainingChartData.push(0);
            }
        }

        if (document.querySelector("#employee-training-chart")) {
            try {
                new Chartist.Line(
                    "#employee-training-chart", {
                        labels: employeeTrainingChartLabels,
                        series: [employeeTrainingChartData],
                    }, {
                        low: 0,
                        showArea: true,
                        plugins: [Chartist.plugins.tooltip()],
                        lineSmooth: Chartist.Interpolation.cardinal({
                            tension: 0.5
                        })
                    }
                );
            } catch (error) {
                console.error('Error rendering training chart:', error);
                // Show message if chart fails to render
                document.querySelector("#employee-training-chart").innerHTML = '<div class="text-center p-4 text-muted">No training data available</div>';
            }
        }
    });
</script>
@endsection