@extends('layouts.master')

@section('css')
    <!-- Table css -->
    <link href="{{ URL::asset('plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css') }}" rel="stylesheet"
        type="text/css" media="screen">
@endsection

@section('breadcrumb')
    <div class="col-sm-6">
        <h4 class="page-title text-left">Salaries (Slip Gaji)</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0);">Salaries</a></li>
        </ol>
    </div>
@endsection

@section('button')
    <div class="btn-group" role="group">
        <a href="{{ route('salaries.create') }}" class="btn btn-primary btn-sm btn-flat">
            <i class="mdi mdi-plus mr-2"></i>Add New Salary
        </a>
        <a href="{{ route('salaries.import') }}" class="btn btn-success btn-sm btn-flat">
            <i class="mdi mdi-upload mr-2"></i>Import Salaries
        </a>
    </div>
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
                                    <th data-priority="2">Month</th>
                                    <th data-priority="3">Basic Salary</th>
                                    <th data-priority="4">Allowances</th>
                                    <th data-priority="5">Deductions</th>
                                    <th data-priority="6">Net Salary</th>
                                    <th data-priority="7">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salaries as $salary)
                                <tr>
                                    <td>
                                        <strong>{{ $salary->employee->name }}</strong><br>
                                        <small class="text-muted">ID: {{ $salary->employee->id }}</small>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($salary->month)->format('F Y') }}</td>
                                    <td>Rp {{ number_format($salary->basic_salary, 0, ',', '.') }}</td>
                                    <td>
                                        @if(is_array($salary->allowances) && count($salary->allowances) > 0)
                                            @foreach($salary->allowances as $key => $value)
                                                <small>{{ $key }}: Rp {{ number_format($value, 0, ',', '.') }}</small><br>
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(is_array($salary->deductions) && count($salary->deductions) > 0)
                                            @foreach($salary->deductions as $key => $value)
                                                <small>{{ $key }}: Rp {{ number_format($value, 0, ',', '.') }}</small><br>
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="text-success">Rp {{ number_format($salary->net_salary, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('salaries.show', $salary) }}" class="btn btn-info btn-sm">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            <a href="{{ route('salaries.edit', $salary) }}" class="btn btn-warning btn-sm">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <form action="{{ route('salaries.destroy', $salary) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this salary record?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="py-4">
                                            <i class="mdi mdi-information-outline" style="font-size: 48px; color: #ccc;"></i>
                                            <p class="mt-2 text-muted">No salary records found.</p>
                                            <a href="{{ route('salaries.create') }}" class="btn btn-primary">
                                                <i class="mdi mdi-plus mr-2"></i>Add First Salary Record
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
