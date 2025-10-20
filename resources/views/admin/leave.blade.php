@extends('layouts.master')

@section('css')
    <!-- Table css -->
    <link href="{{ URL::asset('plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css') }}" rel="stylesheet"
        type="text/css" media="screen">
@endsection

@section('breadcrumb')
    <div class="col-sm-6">
        <h4 class="page-title text-left">Leave</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0);">Leave</a></li>


        </ol>
    </div>
@endsection
@section('button')
    <a href="{{ route('leave.create') }}" class="btn btn-primary btn-sm btn-flat">
        <i class="mdi mdi-plus mr-2"></i>Add New Leave Request
    </a>
@endsection

@section('content')
@include('includes.flash')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-rep-plugin">
                    <div class="table-responsive mb-0" data-pattern="priority-columns">
                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th data-priority="1">ID</th>
                                    <th data-priority="2">Employee</th>
                                    <th data-priority="3">Leave Date</th>
                                    <th data-priority="4">Leave Time</th>
                                    <th data-priority="5">Type</th>
                                    <th data-priority="6">Status</th>
                                    <th data-priority="7">State</th>
                                    <th data-priority="8">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaves as $leave)
                                <tr>
                                    <td>{{ $leave->id }}</td>
                                    <td>
                                        <strong>{{ $leave->employee->name ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ $leave->employee->employee_code ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ $leave->leave_date ? $leave->leave_date->format('M d, Y') : 'N/A' }}</td>
                                    <td>{{ $leave->leave_time ? $leave->leave_time->format('H:i A') : 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $leave->leave_type }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $leave->status_badge }}">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $leave->state ?? 'N/A' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('leave.show', $leave) }}" class="btn btn-sm btn-info" title="View">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            <a href="{{ route('leave.edit', $leave) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            @if($leave->status == 'pending')
                                                <form action="{{ route('leave.approve', $leave) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Approve" onclick="return confirm('Are you sure you want to approve this leave request?')">
                                                        <i class="mdi mdi-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('leave.reject', $leave) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Reject" onclick="return confirm('Are you sure you want to reject this leave request?')">
                                                        <i class="mdi mdi-close"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('leave.destroy', $leave) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this leave request?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#datatable-buttons').DataTable({
        destroy: true,
        responsive: true,
        pageLength: 25,
        order: [[0, 'desc']]
    });
});
</script>
@endsection
