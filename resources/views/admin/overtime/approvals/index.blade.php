@extends('layouts.master')

@section('css')
<style>
    #overtime-approvals-table {
        width: 100% !important;
        min-width: 1400px;
    }

    #overtime-approvals-table th {
        white-space: nowrap;
        padding: 12px 8px;
        font-weight: 600;
    }

    #overtime-approvals-table td {
        padding: 10px 8px;
        vertical-align: middle;
    }

    #overtime-approvals-table th:nth-child(1) {
        width: 60px;
    }

    /* ID */
    #overtime-approvals-table th:nth-child(2) {
        width: 120px;
    }

    /* Employee */
    #overtime-approvals-table th:nth-child(3) {
        width: 150px;
    }

    /* Section */
    #overtime-approvals-table th:nth-child(4) {
        width: 120px;
    }

    /* SDM/HRD */
    #overtime-approvals-table th:nth-child(5) {
        width: 110px;
    }

    /* Date */
    #overtime-approvals-table th:nth-child(6) {
        width: 100px;
    }

    /* Start Time */
    #overtime-approvals-table th:nth-child(7) {
        width: 100px;
    }

    /* End Time */
    #overtime-approvals-table th:nth-child(8) {
        width: 100px;
    }

    /* Duration */
    #overtime-approvals-table th:nth-child(9) {
        width: 200px;
    }

    /* Reason */
    #overtime-approvals-table th:nth-child(10) {
        width: 150px;
    }

    /* Section Approved */
    #overtime-approvals-table th:nth-child(11) {
        width: 150px;
    }

    /* Wadir Approved */
    #overtime-approvals-table th:nth-child(12) {
        width: 150px;
    }

    /* SDM Approved */
    #overtime-approvals-table th:nth-child(13) {
        width: 100px;
    }

    /* Status */
    #overtime-approvals-table th:nth-child(14) {
        width: 200px;
        min-width: 200px;
    }

    /* Actions */

    .table-responsive {
        width: 100%;
    }

    .approval-form {
        margin-right: 8px;
    }

    .approval-form .form-check {
        display: inline-flex;
        align-items: center;
        margin-bottom: 0;
    }

    .approval-form .form-check-label {
        margin-left: 5px;
        margin-bottom: 0;
    }

    .d-flex.gap-2>* {
        margin-right: 8px;
    }

    .d-flex.gap-2>*:last-child {
        margin-right: 0;
    }

    .action-buttons {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-start;
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
    }

    .action-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .action-buttons .btn-success {
        background-color: #28a745;
        color: #fff;
    }

    .action-buttons .btn-success:hover {
        background-color: #218838;
        color: #fff;
    }

    .action-buttons form {
        display: inline-block;
        margin: 0;
    }
</style>
@endsection

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Overtime Approvals</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Overtime</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Approvals</a></li>
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
                <div class="table-responsive" style="overflow-x: auto;">
                    <table id="overtime-approvals-table" class="table table-striped table-bordered" style="width: 100%; min-width: 1400px; border-collapse: collapse; border-spacing: 0;">
                        <thead>
                            <tr>
                                <th data-priority="1">ID</th>
                                <th data-priority="2">Employee</th>
                                <th data-priority="3">Section</th>
                                <th data-priority="4">SDM/HRD</th>
                                <th data-priority="5">Date</th>
                                <th data-priority="6">Start Time</th>
                                <th data-priority="7">End Time</th>
                                <th data-priority="8">Duration</th>
                                <th data-priority="9">Reason</th>
                                <th data-priority="10">Section Approved</th>
                                <th data-priority="11">Wadir Approved</th>
                                <th data-priority="12">SDM Approved</th>
                                <th data-priority="13">Status</th>
                                <th data-priority="14">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($overtimes as $overtime)
                            <tr>
                                <td>{{ $overtime->id_overtime }}</td>
                                <td>{{ $overtime->employee->name ?? 'N/A' }}</td>
                                <td>{{ $overtime->section->name ?? 'N/A' }}</td>
                                <td>{{ $overtime->sdmEmployee->name ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($overtime->overtime_date)->format('M d, Y') }}</td>
                                <td>
                                    @if($overtime->start_time)
                                    @php
                                    // Format time as HH:MM (remove seconds if present)
                                    $time = $overtime->start_time;
                                    if (strpos($time, ':') !== false) {
                                    $parts = explode(':', $time);
                                    echo str_pad($parts[0], 2, '0', STR_PAD_LEFT) . ':' . str_pad($parts[1] ?? '00', 2, '0', STR_PAD_LEFT);
                                    } else {
                                    echo $time;
                                    }
                                    @endphp
                                    @else
                                    N/A
                                    @endif
                                </td>
                                <td>
                                    @if($overtime->end_time)
                                    @php
                                    // Format time as HH:MM (remove seconds if present)
                                    $time = $overtime->end_time;
                                    if (strpos($time, ':') !== false) {
                                    $parts = explode(':', $time);
                                    echo str_pad($parts[0], 2, '0', STR_PAD_LEFT) . ':' . str_pad($parts[1] ?? '00', 2, '0', STR_PAD_LEFT);
                                    } else {
                                    echo $time;
                                    }
                                    @endphp
                                    @else
                                    N/A
                                    @endif
                                </td>
                                <td>
                                    @php
                                    // Convert duration from HH:MM:SS to hours
                                    if ($overtime->duration) {
                                    $durationParts = explode(':', $overtime->duration);
                                    $hours = isset($durationParts[0]) ? (int)$durationParts[0] : 0;
                                    $minutes = isset($durationParts[1]) ? (int)$durationParts[1] : 0;
                                    $seconds = isset($durationParts[2]) ? (int)$durationParts[2] : 0;

                                    // Calculate total hours (including minutes and seconds as decimal)
                                    $totalHours = $hours + ($minutes / 60) + ($seconds / 3600);

                                    // Format: if whole number, show as "X hours", else show decimal
                                    if ($totalHours == (int)$totalHours) {
                                    echo (int)$totalHours . ' hours';
                                    } else {
                                    echo number_format($totalHours, 2) . ' hours';
                                    }
                                    } else {
                                    echo 'N/A';
                                    }
                                    @endphp
                                </td>
                                <td>{{ Str::limit($overtime->reason, 50) }}</td>
                                <td class="text-center">
                                    @if($overtime->section_approved)
                                    <span class="badge badge-success">
                                        <i class="mdi mdi-check"></i> Approved
                                    </span>
                                    @if($overtime->sectionApprover)
                                    <br><small class="text-muted"><strong>By:</strong> {{ $overtime->sectionApprover->name }}</small>
                                    @endif
                                    @if($overtime->section_approved_at)
                                    <br><small class="text-muted">{{ \Carbon\Carbon::parse($overtime->section_approved_at)->format('M d, Y H:i') }}</small>
                                    @endif
                                    @else
                                    <span class="badge badge-warning">Pending</span>
                                    @if($overtime->sectionEmployee)
                                    <br><small class="text-muted"><strong>Waiting:</strong> {{ $overtime->sectionEmployee->name }}</small>
                                    @endif
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($overtime->wadir_approved)
                                    <span class="badge badge-success">
                                        <i class="mdi mdi-check"></i> Approved
                                    </span>
                                    @if($overtime->wadirApprover)
                                    <br><small class="text-muted"><strong>By:</strong> {{ $overtime->wadirApprover->name }}</small>
                                    @endif
                                    @if($overtime->wadir_approved_at)
                                    <br><small class="text-muted">{{ \Carbon\Carbon::parse($overtime->wadir_approved_at)->format('M d, Y H:i') }}</small>
                                    @endif
                                    @else
                                    <span class="badge badge-warning">Pending</span>
                                    @if($overtime->wadirEmployee)
                                    <br><small class="text-muted"><strong>Waiting:</strong> {{ $overtime->wadirEmployee->name }}</small>
                                    @endif
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($overtime->sdm_approved)
                                    <span class="badge badge-success">
                                        <i class="mdi mdi-check"></i> Approved
                                    </span>
                                    @if($overtime->sdmApprover)
                                    <br><small class="text-muted"><strong>By:</strong> {{ $overtime->sdmApprover->name }}</small>
                                    @endif
                                    @if($overtime->sdm_approved_at)
                                    <br><small class="text-muted">{{ \Carbon\Carbon::parse($overtime->sdm_approved_at)->format('M d, Y H:i') }}</small>
                                    @endif
                                    @else
                                    <span class="badge badge-warning">Pending</span>
                                    @if($overtime->sdmEmployee)
                                    <br><small class="text-muted"><strong>Waiting:</strong> {{ $overtime->sdmEmployee->name }}</small>
                                    @endif
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $overtime->status == 'approved' ? 'success' : ($overtime->status == 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($overtime->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2" style="flex-wrap: nowrap;">
                                        @if($overtime->status == 'pending')
                                        @php
                                        $employee = Auth::guard('employee')->check() ? Auth::guard('employee')->user() : null;
                                        $user = Auth::guard('web')->user();

                                        if ($employee) {
                                        $position = $employee->position;
                                        $isSection = strpos($position, 'Section ') === 0;
                                        $isWadir = in_array($position, ['Wadir 1', 'Wadir 2']);
                                        $isSdm = $position === 'SDM/HRD';
                                        } else {
                                        $isSection = $user && $user->hasRole('section');
                                        $isWadir = false;
                                        $isSdm = $user && $user->hasRole('admin_sdm');
                                        }
                                        $isSuperAdmin = $user && $user->hasRole('super_admin');
                                        @endphp

                                        {{-- Approval Buttons (Pojok Kiri) --}}
                                        <div class="d-flex align-items-center action-buttons">
                                            {{-- Section: Hanya bisa approve sebagai section --}}
                                            @if($isSection && !$overtime->section_approved)
                                            <form action="{{ route('overtime.approve.section', $overtime) }}" method="POST" class="approve-form" data-approval-type="Section" style="display:inline-block; margin:0;">
                                                @csrf
                                                <button type="button" class="btn btn-success btn-sm approve-btn" title="Approve as Section" data-toggle="tooltip" data-placement="top">
                                                    <i class="mdi mdi-check-circle-outline"></i>
                                                </button>
                                            </form>
                                            @endif

                                            {{-- Wadir: Hanya bisa approve sebagai wadir, dan hanya jika section sudah approve --}}
                                            @if($isWadir && !$overtime->wadir_approved && $overtime->section_approved)
                                            <form action="{{ route('overtime.approve.wadir', $overtime) }}" method="POST" class="approve-form" data-approval-type="Wadir" style="display:inline-block; margin:0;">
                                                @csrf
                                                <button type="button" class="btn btn-success btn-sm approve-btn" title="Approve as Wadir" data-toggle="tooltip" data-placement="top">
                                                    <i class="mdi mdi-check-circle-outline"></i>
                                                </button>
                                            </form>
                                            @endif

                                            {{-- SDM: Hanya bisa approve sebagai sdm, dan hanya jika section dan wadir sudah approve --}}
                                            @if($isSdm && !$overtime->sdm_approved && $overtime->section_approved && $overtime->wadir_approved)
                                            <form action="{{ route('overtime.approve.sdm', $overtime) }}" method="POST" class="approve-form" data-approval-type="SDM" style="display:inline-block; margin:0;">
                                                @csrf
                                                <button type="button" class="btn btn-success btn-sm approve-btn" title="Approve as SDM" data-toggle="tooltip" data-placement="top">
                                                    <i class="mdi mdi-check-circle-outline"></i>
                                                </button>
                                            </form>
                                            @endif

                                            {{-- Super Admin: Bisa approve semua level, tapi tetap mengikuti urutan (section -> wadir -> sdm) --}}
                                            @if($isSuperAdmin)
                                            @if(!$overtime->section_approved)
                                            <form action="{{ route('overtime.approve.section', $overtime) }}" method="POST" class="approve-form" data-approval-type="Section" style="display:inline-block; margin:0;">
                                                @csrf
                                                <button type="button" class="btn btn-success btn-sm approve-btn" title="Approve as Section" data-toggle="tooltip" data-placement="top">
                                                    <i class="mdi mdi-check-circle-outline"></i>
                                                </button>
                                            </form>
                                            @endif
                                            @if(!$overtime->wadir_approved && $overtime->section_approved)
                                            <form action="{{ route('overtime.approve.wadir', $overtime) }}" method="POST" class="approve-form" data-approval-type="Wadir" style="display:inline-block; margin:0;">
                                                @csrf
                                                <button type="button" class="btn btn-success btn-sm approve-btn" title="Approve as Wadir" data-toggle="tooltip" data-placement="top">
                                                    <i class="mdi mdi-check-circle-outline"></i>
                                                </button>
                                            </form>
                                            @endif
                                            @if(!$overtime->sdm_approved && $overtime->section_approved && $overtime->wadir_approved)
                                            <form action="{{ route('overtime.approve.sdm', $overtime) }}" method="POST" class="approve-form" data-approval-type="SDM" style="display:inline-block; margin:0;">
                                                @csrf
                                                <button type="button" class="btn btn-success btn-sm approve-btn" title="Approve as SDM" data-toggle="tooltip" data-placement="top">
                                                    <i class="mdi mdi-check-circle-outline"></i>
                                                </button>
                                            </form>
                                            @endif
                                            @endif
                                        </div>

                                        {{-- View Button --}}
                                        <a href="{{ route('overtime.show', ['overtime' => $overtime, 'from' => 'approvals']) }}" class="btn btn-info btn-sm" title="View Details" data-toggle="tooltip" data-placement="top">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </a>

                                        {{-- Reject Button --}}
                                        <form action="{{ route('overtime.reject', $overtime) }}" method="POST" class="d-inline mb-0 reject-form" style="display:inline-block; margin:0;">
                                            @csrf
                                            <button type="button" class="btn btn-danger btn-sm reject-btn" title="Reject" data-toggle="tooltip" data-placement="top" data-overtime-id="{{ $overtime->id_overtime }}">
                                                <i class="mdi mdi-close-circle-outline"></i>
                                            </button>
                                        </form>
                                        @else
                                        {{-- Jika status bukan pending, hanya tampilkan View button --}}
                                        <a href="{{ route('overtime.show', ['overtime' => $overtime, 'from' => 'approvals']) }}" class="btn btn-info btn-sm" title="View Details" data-toggle="tooltip" data-placement="top">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="14" class="text-center">No overtime requests pending approval.</td>
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

        // Don't initialize DataTables for approvals page due to complex HTML elements (forms, checkboxes) in cells
        // This causes DataTables errors. Use simple table styling instead.
        var table = $('#overtime-approvals-table');

        // Add simple table styling
        table.addClass('table-hover');

        // Handle approve button with SweetAlert
        $('.approve-btn').on('click', function(e) {
            e.preventDefault();
            var form = $(this).closest('.approve-form');
            var approvalType = form.data('approval-type');
            var row = form.closest('tr');
            var employeeName = row.find('td:eq(1)').text().trim();

            // Clean employee name (remove extra whitespace)
            employeeName = employeeName.replace(/\s+/g, ' ').trim();

            if (typeof swal !== 'undefined') {
                swal({
                    title: "Konfirmasi Approval",
                    text: "Apakah Anda yakin ingin approve overtime request dari " + employeeName + " sebagai " + approvalType + "?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: "Batal",
                            value: false,
                            visible: true,
                            className: "btn btn-secondary",
                            closeModal: true,
                        },
                        confirm: {
                            text: "Ya, Approve",
                            value: true,
                            visible: true,
                            className: "btn btn-success",
                            closeModal: false
                        }
                    },
                    dangerMode: false,
                }).then((willApprove) => {
                    if (willApprove) {
                        // Show loading
                        swal({
                            title: "Memproses...",
                            text: "Sedang memproses approval...",
                            icon: "info",
                            button: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                        });

                        // Submit form via AJAX
                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: form.serialize(),
                            success: function(response) {
                                swal({
                                    title: "Berhasil!",
                                    text: "Overtime request berhasil di-approve sebagai " + approvalType,
                                    icon: "success",
                                    button: "OK",
                                    timer: 2000,
                                }).then(() => {
                                    // Reload page to show updated status
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                var errorMessage = "Terjadi kesalahan saat memproses approval.";
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }

                                swal({
                                    title: "Error!",
                                    text: errorMessage,
                                    icon: "error",
                                    button: "OK",
                                });
                            }
                        });
                    }
                });
            } else {
                // Fallback to native confirm if SweetAlert is not available
                if (confirm('Apakah Anda yakin ingin approve overtime request ini sebagai ' + approvalType + '?')) {
                    form.submit();
                }
            }
        });

        // Handle reject button with SweetAlert
        $('.reject-btn').on('click', function(e) {
            e.preventDefault();
            var form = $(this).closest('.reject-form');
            var overtimeId = $(this).data('overtime-id');

            if (typeof swal !== 'undefined') {
                swal({
                    title: "Konfirmasi Reject",
                    text: "Apakah Anda yakin ingin menolak (reject) overtime request ini?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: "Batal",
                            value: false,
                            visible: true,
                            className: "btn btn-secondary",
                            closeModal: true,
                        },
                        confirm: {
                            text: "Ya, Reject",
                            value: true,
                            visible: true,
                            className: "btn btn-danger",
                            closeModal: false
                        }
                    },
                    dangerMode: true,
                }).then((willReject) => {
                    if (willReject) {
                        // Show loading
                        swal({
                            title: "Memproses...",
                            text: "Sedang memproses reject...",
                            icon: "info",
                            button: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false,
                        });

                        // Submit form via AJAX
                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: form.serialize(),
                            success: function(response) {
                                swal({
                                    title: "Berhasil!",
                                    text: "Overtime request berhasil di-reject",
                                    icon: "success",
                                    button: "OK",
                                    timer: 2000,
                                }).then(() => {
                                    // Reload page to show updated status
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                var errorMessage = "Terjadi kesalahan saat memproses reject.";
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }

                                swal({
                                    title: "Error!",
                                    text: errorMessage,
                                    icon: "error",
                                    button: "OK",
                                });
                            }
                        });
                    }
                });
            } else {
                // Fallback to native confirm if SweetAlert is not available
                if (confirm('Are you sure you want to reject this overtime request?')) {
                    form.submit();
                }
            }
        });
    });
</script>
@endsection