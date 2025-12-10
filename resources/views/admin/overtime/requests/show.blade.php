@extends('layouts.master')

@section('css')
@endsection

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Overtime Request Details</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Overtime</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Request Details</a></li>
    </ol>
</div>
@endsection

@section('button')
<div class="btn-group" role="group">
    @php
    $backRoute = 'overtime.requests';
    $backLabel = 'Back to Requests';

    if (isset($from)) {
    switch($from) {
    case 'approvals':
    $backRoute = 'overtime.approvals';
    $backLabel = 'Back to Approvals';
    break;
    case 'reports':
    $backRoute = 'overtime.reports.index';
    $backLabel = 'Back to Reports';
    break;
    case 'index':
    $backRoute = 'indexOvertime';
    $backLabel = 'Back to Overtime';
    break;
    case 'requests':
    default:
    $backRoute = 'overtime.requests';
    $backLabel = 'Back to Requests';
    break;
    }
    }
    @endphp
    <a href="{{ route($backRoute) }}" class="btn btn-secondary btn-sm btn-flat">
        <i class="mdi mdi-arrow-left mr-2"></i>{{ $backLabel }}
    </a>
    @php
    // Check if user can edit/delete
    // Only super_admin and SDM/HRD can edit/delete
    $isEmployee = Auth::guard('employee')->check();
    $user = Auth::guard('employee')->check() ? Auth::guard('employee')->user() : Auth::guard('web')->user();

    $canEditDelete = false;

    if ($isEmployee) {
    // Employee: check position
    $position = $user->position ?? '';
    // Only SDM/HRD position can edit/delete
    // Employees, Magang, PKL cannot edit/delete
    if ($position === 'SDM/HRD') {
    $canEditDelete = true;
    }
    } else {
    // Admin: check roles
    if ($user) {
    $userRoles = $user->roles()->pluck('slug')->toArray();
    // Only super_admin and admin_sdm can edit/delete
    $canEditDelete = in_array('super_admin', $userRoles) || in_array('admin_sdm', $userRoles);
    }
    }
    @endphp
    @if($overtime->status == 'pending' && $canEditDelete)
    <a href="{{ route('overtime.edit', $overtime) }}" class="btn btn-warning btn-sm btn-flat">
        <i class="mdi mdi-pencil mr-2"></i>Edit Request
    </a>
    @endif
</div>
@endsection

@section('content')
@include('includes.flash')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="card-title">Overtime Request Information</h5>

                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Request ID:</strong></td>
                                <td>#{{ $overtime->id_overtime }}</td>
                            </tr>
                            <tr>
                                <td><strong>Employee:</strong></td>
                                <td>{{ $overtime->employee->name ?? 'N/A' }} ({{ $overtime->employee->employee_code ?? 'N/A' }})</td>
                            </tr>
                            <tr>
                                <td><strong>Overtime Date:</strong></td>
                                <td>{{ \Carbon\Carbon::parse($overtime->overtime_date)->format('M d, Y') }}</td>
                            </tr>
                            @if($overtime->start_time && $overtime->end_time)
                            <tr>
                                <td><strong>Start Time:</strong></td>
                                <td>{{ \Carbon\Carbon::parse($overtime->start_time)->format('H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>End Time:</strong></td>
                                <td>{{ \Carbon\Carbon::parse($overtime->end_time)->format('H:i') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong>Duration:</strong></td>
                                <td>{{ $overtime->duration }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span class="badge badge-{{ $overtime->status == 'approved' ? 'success' : ($overtime->status == 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($overtime->status) }}
                                    </span>
                                </td>
                            </tr>
                            @if($overtime->approved_by)
                            <tr>
                                <td><strong>Approved By:</strong></td>
                                <td>{{ $overtime->approver->name ?? 'N/A' }}</td>
                            </tr>
                            @endif
                            @if($overtime->approved_at)
                            <tr>
                                <td><strong>Approved At:</strong></td>
                                <td>{{ \Carbon\Carbon::parse($overtime->approved_at)->format('M d, Y H:i') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong>Created At:</strong></td>
                                <td>{{ \Carbon\Carbon::parse($overtime->created_at)->format('M d, Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Reason</h6>
                            </div>
                            <div class="card-body">
                                <p class="card-text">{{ $overtime->reason }}</p>
                            </div>
                        </div>

                        @if($overtime->status == 'pending' && auth()->user()->hasRole(['super_admin', 'admin_sdm', 'wadir']))
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Actions</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('overtime.approve', $overtime) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm btn-block"
                                        onclick="return confirm('Are you sure you want to approve this overtime request?')">
                                        <i class="mdi mdi-check mr-2"></i>Approve
                                    </button>
                                </form>

                                <form action="{{ route('overtime.reject', $overtime) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm btn-block mt-2"
                                        onclick="return confirm('Are you sure you want to reject this overtime request?')">
                                        <i class="mdi mdi-close mr-2"></i>Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Any additional JavaScript if needed
    });
</script>
@endsection