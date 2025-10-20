@extends('layouts.master')

@section('css')
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
                                <th data-priority="4">Start Time</th>
                                <th data-priority="5">End Time</th>
                                <th data-priority="6">Duration</th>
                                <th data-priority="7">Reason</th>
                                <th data-priority="8">Status</th>
                                <th data-priority="9">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($overtimes as $overtime)
                            <tr>
                                <td>{{ $overtime->id }}</td>
                                <td>{{ $overtime->employee->name ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($overtime->overtime_date)->format('M d, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($overtime->start_time)->format('H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($overtime->end_time)->format('H:i') }}</td>
                                <td>
                                    @php
                                        $start = \Carbon\Carbon::parse($overtime->start_time);
                                        $end = \Carbon\Carbon::parse($overtime->end_time);
                                        $duration = $start->diffInHours($end);
                                    @endphp
                                    {{ $duration }} hours
                                </td>
                                <td>{{ Str::limit($overtime->reason, 50) }}</td>
                                <td>
                                    <span class="badge badge-{{ $overtime->status == 'approved' ? 'success' : ($overtime->status == 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($overtime->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('overtime.show', $overtime) }}" class="btn btn-info btn-sm">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                        <a href="{{ route('overtime.edit', $overtime) }}" class="btn btn-warning btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <form action="{{ route('overtime.destroy', $overtime) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this overtime request?')">
                                                <i class="mdi mdi-trash"></i>
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
        $('#datatable-buttons').DataTable({
            destroy: true,
            responsive: true,
            pageLength: 25,
            order: [[0, 'desc']]
        });
    });
</script>
@endsection
