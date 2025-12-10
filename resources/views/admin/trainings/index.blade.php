@extends('layouts.master')

@section('css')
<!-- Table css -->
<link href="{{ URL::asset('plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css') }}" rel="stylesheet"
    type="text/css" media="screen">
@endsection

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Trainings</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Trainings</a></li>
    </ol>
</div>
@endsection

@section('button')
<a href="{{ route('trainings.create') }}" class="btn btn-primary btn-sm btn-flat">
    <i class="mdi mdi-plus mr-2"></i>Add New Training
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
                                    <th data-priority="1">Employee</th>
                                    <th data-priority="2">Training Title</th>
                                    <th data-priority="3">Category</th>
                                    <th data-priority="4">Start Date</th>
                                    <th data-priority="5">End Date</th>
                                    <th data-priority="6">Status</th>
                                    <th data-priority="7">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trainings as $training)
                                <tr>
                                    <td>
                                        <strong>{{ $training->employee->name }}</strong><br>
                                        <small class="text-muted">ID: {{ $training->employee->id_employees }}</small>
                                    </td>
                                    <td>{{ $training->title }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ ucfirst($training->category) }}</span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($training->start_date)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($training->end_date)->format('d M Y') }}</td>
                                    <td>
                                        @switch($training->status)
                                        @case('planned')
                                        <span class="badge badge-secondary">Planned</span>
                                        @break
                                        @case('ongoing')
                                        <span class="badge badge-warning">Ongoing</span>
                                        @break
                                        @case('completed')
                                        <span class="badge badge-success">Completed</span>
                                        @break
                                        @case('cancelled')
                                        <span class="badge badge-danger">Cancelled</span>
                                        @break
                                        @default
                                        <span class="badge badge-light">{{ ucfirst($training->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('trainings.show', $training) }}" class="btn btn-info" title="View Details" data-toggle="tooltip" data-placement="top">
                                                <i class="mdi mdi-eye-outline"></i>
                                            </a>
                                            <a href="{{ route('trainings.edit', $training) }}" class="btn btn-warning" title="Edit" data-toggle="tooltip" data-placement="top">
                                                <i class="mdi mdi-pencil-outline"></i>
                                            </a>
                                            <form action="{{ route('trainings.destroy', $training) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Delete" data-toggle="tooltip" data-placement="top" onclick="return confirm('Are you sure you want to delete this training record?')">
                                                    <i class="mdi mdi-delete-outline"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="py-4">
                                            <i class="mdi mdi-school" style="font-size: 48px; color: #ccc;"></i>
                                            <p class="mt-2 text-muted">No training records found.</p>
                                            <a href="{{ route('trainings.create') }}" class="btn btn-primary">
                                                <i class="mdi mdi-plus mr-2"></i>Add First Training Record
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
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