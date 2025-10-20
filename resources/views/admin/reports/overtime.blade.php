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
    <a href="/overtime" class="btn btn-secondary btn-sm btn-flat">
        <i class="mdi mdi-arrow-left mr-2"></i>Back to Overtime
    </a>
@endsection

@section('content')
@include('includes.flash')

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
                                       value="{{ $startDate }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="end_date">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" 
                                       value="{{ $endDate }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="employee_id">Employee</label>
                                <select name="employee_id" id="employee_id" class="form-control">
                                    <option value="">All Employees</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ $employeeId == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }} (ID: {{ $employee->id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
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
                    Period: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                    @if($employeeId)
                        | Employee: {{ $employees->where('id', $employeeId)->first()->name ?? 'Unknown' }}
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
                                    <th data-priority="3">Duration</th>
                                    <th data-priority="4">Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($overtimes as $overtime)
                                <tr>
                                    <td>
                                        <strong>{{ $overtime->employee->name }}</strong><br>
                                        <small class="text-muted">ID: {{ $overtime->employee->id }}</small>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($overtime->overtime_date)->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $overtime->duration }}</span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($overtime->created_at)->format('d M Y H:i:s') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">
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
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6>Summary</h6>
                                <p class="mb-1"><strong>Total Overtime Records:</strong> {{ $overtimes->count() }}</p>
                                <p class="mb-1"><strong>Total Employees:</strong> {{ $overtimes->pluck('employee_id')->unique()->count() }}</p>
                                <p class="mb-0"><strong>Date Range:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6>Top Overtime Employees</h6>
                                @php
                                    $topEmployees = $overtimes->groupBy('employee_id')->map(function($group) {
                                        return [
                                            'employee' => $group->first()->employee,
                                            'count' => $group->count()
                                        ];
                                    })->sortByDesc('count')->take(3);
                                @endphp
                                @if($topEmployees->count() > 0)
                                    @foreach($topEmployees as $item)
                                        <p class="mb-1">
                                            <strong>{{ $item['employee']->name }}</strong>: 
                                            <span class="badge badge-primary">{{ $item['count'] }} times</span>
                                        </p>
                                    @endforeach
                                @else
                                    <p class="mb-0 text-muted">No data available</p>
                                @endif
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
