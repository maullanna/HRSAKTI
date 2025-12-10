@extends('layouts.master')

@section('css')
<style>
    .action-buttons {
        display: flex;
        gap: 4px;
        align-items: center;
        justify-content: center;
    }

    .action-buttons form {
        display: inline-block;
        margin: 0;
        padding: 0;
    }

    .action-buttons .btn {
        margin: 0;
        padding: 6px 10px;
        border-radius: 4px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: none;
        min-width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .action-buttons .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    .action-buttons .btn:active {
        transform: translateY(0);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .action-buttons .btn i {
        font-size: 16px;
        line-height: 1;
    }

    .action-buttons .btn-info {
        background-color: #17a2b8;
        color: #fff;
    }

    .action-buttons .btn-info:hover {
        background-color: #138496;
        color: #fff;
    }

    .action-buttons .btn-warning {
        background-color: #ffc107;
        color: #212529;
    }

    .action-buttons .btn-warning:hover {
        background-color: #e0a800;
        color: #212529;
    }

    .action-buttons .btn-danger {
        background-color: #dc3545;
        color: #fff;
    }

    .action-buttons .btn-danger:hover {
        background-color: #c82333;
        color: #fff;
    }
</style>
@endsection

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Overtime Requests</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Overtime</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Requests</a></li>
    </ol>
</div>
@endsection

@section('button')
<a href="{{ route('overtime.create') }}" class="btn btn-primary btn-sm btn-flat">
    <i class="mdi mdi-plus mr-2"></i>Add Overtime Request
</a>
@endsection

@section('content')
@include('includes.flash')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th data-priority="1">ID</th>
                                <th data-priority="2">Employee</th>
                                <th data-priority="3">Date</th>
                                <th data-priority="4">Duration</th>
                                <th data-priority="7">Reason</th>
                                <th data-priority="8">Status</th>
                                <th data-priority="9">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($overtimes as $overtime)
                            <tr>
                                <td>{{ $overtime->id_overtime }}</td>
                                <td>{{ $overtime->employee->name ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($overtime->overtime_date)->format('M d, Y') }}</td>
                                <td>{{ $overtime->duration }}</td>
                                <td>{{ Str::limit($overtime->reason, 50) }}</td>
                                <td>
                                    <span class="badge badge-{{ $overtime->status == 'approved' ? 'success' : ($overtime->status == 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($overtime->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('overtime.show', ['overtime' => $overtime, 'from' => 'requests']) }}" class="btn btn-info" title="View Details" data-toggle="tooltip" data-placement="top">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </a>
                                        <a href="{{ route('overtime.edit', $overtime) }}" class="btn btn-warning" title="Edit" data-toggle="tooltip" data-placement="top">
                                            <i class="mdi mdi-pencil-outline"></i>
                                        </a>
                                        <form action="{{ route('overtime.destroy', $overtime) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Delete" data-toggle="tooltip" data-placement="top" onclick="return confirm('Are you sure you want to delete this overtime request?')">
                                                <i class="mdi mdi-delete-outline"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">No overtime requests found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Initialize DataTable
        $('#datatable-buttons').DataTable({
            destroy: true,
            responsive: true,
            pageLength: 25,
            order: [
                [0, 'desc']
            ]
        });
    });
</script>
@endsection