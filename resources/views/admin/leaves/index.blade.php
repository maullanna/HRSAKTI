@extends('layouts.master')

@section('css')
<!-- Table CSS -->
<link href="{{ URL::asset('plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css') }}" rel="stylesheet" type="text/css" media="screen">

<style>
    /* --- Layout Fix: pastikan konten tidak tertutup sidebar --- */
    body:not(.enlarged) .content-page {
        margin-left: 240px;
        width: calc(100% - 240px);
        transition: all 0.3s ease;
    }

    body.enlarged .content-page {
        margin-left: 70px;
        width: calc(100% - 70px);
        transition: all 0.3s ease;
    }

    @media (max-width: 992px) {
        .content-page {
            margin-left: 0 !important;
            width: 100% !important;
        }
    }

    /* --- Container spacing --- */
    .content-page .container-fluid {
        padding: 20px;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    /* --- Card --- */
    .card {
        width: 100%;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    /* --- Table responsive wrapper --- */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        table.dataTable thead th {
            padding: 10px 12px;
            font-size: 11px;
        }

        table.dataTable tbody td {
            padding: 10px 12px;
            font-size: 13px;
        }

        .action-buttons {
            gap: 6px;
            min-width: auto;
        }

        .action-buttons .btn {
            min-width: 32px;
            height: 32px;
            padding: 5px 8px;
            font-size: 12px;
        }

        .action-buttons .btn i {
            font-size: 16px;
        }
    }

    /* Pastikan link di action buttons bisa diklik */
    .action-buttons a {
        pointer-events: auto !important;
        cursor: pointer !important;
        z-index: 10 !important;
        position: relative !important;
    }

    @media (max-width: 576px) {
        .action-buttons {
            flex-direction: column;
            gap: 5px;
            width: 100%;
        }

        .action-buttons .btn {
            width: 100%;
            justify-content: center;
        }

        table.dataTable thead th,
        table.dataTable tbody td {
            padding: 8px 10px;
            font-size: 12px;
        }
    }

    /* --- Table style --- */
    table.dataTable {
        width: 100% !important;
        border-collapse: separate;
        border-spacing: 0;
        background-color: #fff;
    }

    table.dataTable thead th {
        background-color: #667eea;
        color: #fff;
        text-transform: uppercase;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
        white-space: nowrap;
        padding: 12px 15px;
        border: none;
    }

    table.dataTable tbody td {
        padding: 14px 18px;
        vertical-align: middle;
        border-top: 1px solid #e9ecef;
        border-right: 1px solid #e9ecef;
        font-size: 14px;
        color: #495057;
    }

    table.dataTable tbody td:last-child {
        border-right: none;
    }

    table.dataTable tbody tr {
        transition: all 0.2s ease;
    }

    table.dataTable tbody tr:hover {
        background-color: #f8f9fa;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    table.dataTable tbody tr:last-child td {
        border-bottom: 1px solid #e9ecef;
    }

    /* --- Badge style --- */
    .badge {
        font-size: 11px;
        padding: 6px 12px;
        font-weight: 600;
        border-radius: 6px;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }

    .badge-info {
        background-color: #17a2b8;
        color: #fff;
    }

    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }

    .badge-success {
        background-color: #28a745;
        color: #fff;
    }

    .badge-danger {
        background-color: #dc3545;
        color: #fff;
    }

    /* --- Action buttons --- */
    .action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-start;
        min-width: 200px;
    }

    .action-buttons .btn {
        padding: 6px 10px;
        font-size: 13px;
        border-radius: 5px;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: none;
        min-width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .action-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .action-buttons .btn-info {
        background-color: #17a2b8;
        color: #fff;
    }

    .action-buttons .btn-info:hover {
        background-color: #138496;
    }

    .action-buttons .btn-warning {
        background-color: #ffc107;
        color: #212529;
    }

    .action-buttons .btn-warning:hover {
        background-color: #e0a800;
    }

    .action-buttons .btn-success {
        background-color: #28a745;
        color: #fff;
    }

    .action-buttons .btn-success:hover {
        background-color: #218838;
    }

    .action-buttons .btn-danger {
        background-color: #dc3545;
        color: #fff;
    }

    .action-buttons .btn-danger:hover {
        background-color: #c82333;
    }

    .action-buttons form {
        display: inline-block;
        margin: 0;
    }

    /* --- Employee name styling --- */
    table.dataTable tbody td strong {
        font-size: 15px;
        font-weight: 600;
        color: #212529;
        display: block;
        margin-bottom: 4px;
    }

    table.dataTable tbody td small {
        font-size: 12px;
        color: #6c757d;
    }

    /* --- State column --- */
    table.dataTable tbody td:nth-child(7) {
        max-width: 250px;
        word-wrap: break-word;
        word-break: break-word;
        line-height: 1.5;
    }
</style>
@endsection

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Leave</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active">Leave</li>
    </ol>
</div>
@endsection

@section('button')
<a href="{{ route('leave.create') }}" class="btn btn-primary btn-sm">
    <i class="mdi mdi-plus mr-2"></i>Add New Leave Request
</a>
@endsection

@section('content')
@include('includes.flash')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable-buttons" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Employee</th>
                                <th>Leave Date</th>
                                <th>Leave Time</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>State</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaves as $leave)
                            <tr>
                                <td>{{ $leave->id_leave }}</td>
                                <td>
                                    <strong>{{ $leave->employee->name ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">ID: {{ $leave->employee->id_employees ?? 'N/A' }}</small>
                                </td>
                                <td>{{ $leave->leave_date ? $leave->leave_date->format('M d, Y') : 'N/A' }}</td>
                                <td>{{ $leave->leave_time ? $leave->leave_time->format('H:i A') : 'N/A' }}</td>
                                <td><span class="badge badge-info">{{ $leave->leave_type }}</span></td>
                                <td><span class="badge badge-{{ $leave->status_badge }}">{{ ucfirst($leave->status) }}</span></td>
                                <td>{{ $leave->state ?? 'N/A' }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('leave.show', $leave) }}" class="btn btn-info" title="View Details" data-toggle="tooltip" data-placement="top">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </a>
                                        <a href="{{ route('leave.edit', $leave->id_leave) }}" class="btn btn-warning" title="Edit" data-toggle="tooltip" data-placement="top">
                                            <i class="mdi mdi-pencil-outline"></i>
                                        </a>
                                        @if($leave->status == 'pending')
                                        <form action="{{ route('leave.approve', $leave) }}" method="POST" style="display:inline-block; margin:0;">
                                            @csrf
                                            <button type="submit" class="btn btn-success" title="Approve" data-toggle="tooltip" data-placement="top" onclick="return confirm('Approve this leave request?')">
                                                <i class="mdi mdi-check-circle-outline"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('leave.reject', $leave) }}" method="POST" style="display:inline-block; margin:0;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger" title="Reject" data-toggle="tooltip" data-placement="top" onclick="return confirm('Reject this leave request?')">
                                                <i class="mdi mdi-close-circle-outline"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <form action="{{ route('leave.destroy', $leave) }}" method="POST" style="display:inline-block; margin:0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Delete" data-toggle="tooltip" data-placement="top" onclick="return confirm('Delete this leave request?')">
                                                <i class="mdi mdi-delete-outline"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div> <!-- /.table-responsive -->
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
            responsive: true,
            pageLength: 25,
            order: [
                [0, 'desc']
            ],
            autoWidth: false,
            scrollX: false,
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "No data available",
                paginate: {
                    next: "Next",
                    previous: "Prev"
                }
            },
            columnDefs: [{
                    responsivePriority: 1,
                    targets: 0
                }, // ID
                {
                    responsivePriority: 2,
                    targets: 1
                }, // Employee
                {
                    responsivePriority: 3,
                    targets: 7
                }, // Actions
                {
                    responsivePriority: 4,
                    targets: 6
                }, // State
                {
                    orderable: false,
                    targets: 7 // Actions column tidak bisa di-sort
                }
            ],
            drawCallback: function() {
                // Reinitialize tooltips after table redraw
                $('[data-toggle="tooltip"]').tooltip();
            }
        });

        // Pastikan link di action buttons bekerja dengan benar
        $(document).on('click', '.action-buttons a', function(e) {
            // Biarkan link bekerja normal, tidak ada preventDefault
            // Hanya pastikan tooltip tidak mengganggu
            $(this).tooltip('hide');
        });
    });
</script>
@endsection