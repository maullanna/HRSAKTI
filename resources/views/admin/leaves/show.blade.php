@extends('layouts.master')

@section('title')
    Leave Request Details
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Leave Request Details</h4>
                <div class="float-right">
                    <a href="{{ route('leave.index') }}" class="btn btn-secondary">Back to List</a>
                    <a href="{{ route('leave.edit', $leave) }}" class="btn btn-warning">Edit</a>
                </div>
            </div>
            <div class="card-body">
                @include('includes.flash')

                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">ID:</th>
                                <td>{{ $leave->id }}</td>
                            </tr>
                            <tr>
                                <th>Employee:</th>
                                <td>
                                    <strong>{{ $leave->employee->name ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $leave->employee->employee_code ?? 'N/A' }}</small>
                                </td>
                            </tr>
                            <tr>
                                <th>Leave Date:</th>
                                <td>{{ $leave->leave_date ? $leave->leave_date->format('M d, Y') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Leave Time:</th>
                                <td>{{ $leave->leave_time ? $leave->leave_time->format('M d, Y H:i A') : 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">Type:</th>
                                <td>
                                    <span class="badge badge-info">{{ $leave->leave_type }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <span class="badge badge-{{ $leave->status_badge }}">
                                        {{ ucfirst($leave->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>State/Reason:</th>
                                <td>{{ $leave->state ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Created:</th>
                                <td>{{ $leave->created_at->format('M d, Y H:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($leave->status == 'pending')
                <div class="row mt-4">
                    <div class="col-12">
                        <h5>Actions</h5>
                        <div class="btn-group">
                            <form action="{{ route('leave.approve', $leave) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this leave request?')">
                                    <i class="mdi mdi-check mr-2"></i>Approve
                                </button>
                            </form>
                            <form action="{{ route('leave.reject', $leave) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this leave request?')">
                                    <i class="mdi mdi-close mr-2"></i>Reject
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

