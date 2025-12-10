@extends('layouts.master')

@section('breadcrumb')
    <div class="col-sm-6">
        <h4 class="page-title text-left">Training Details</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('trainings.index') }}">Trainings</a></li>
            <li class="breadcrumb-item active">Details</li>
        </ol>
    </div>
@endsection

@section('content')
@include('includes.flash')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">{{ $training->title }}</h5>
                <div class="card-tools">
                    <a href="{{ route('trainings.edit', $training) }}" class="btn btn-warning btn-sm">
                        <i class="mdi mdi-pencil mr-1"></i>Edit
                    </a>
                    <a href="{{ route('trainings.index') }}" class="btn btn-secondary btn-sm">
                        <i class="mdi mdi-arrow-left mr-1"></i>Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Training Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Title:</strong></td>
                                <td>{{ $training->title }}</td>
                            </tr>
                            <tr>
                                <td><strong>Category:</strong></td>
                                <td>
                                    <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $training->category)) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
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
                            </tr>
                            <tr>
                                <td><strong>Start Date:</strong></td>
                                <td>{{ \Carbon\Carbon::parse($training->start_date)->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>End Date:</strong></td>
                                <td>{{ \Carbon\Carbon::parse($training->end_date)->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Duration:</strong></td>
                                <td>
                                    @php
                                        $start = \Carbon\Carbon::parse($training->start_date);
                                        $end = \Carbon\Carbon::parse($training->end_date);
                                        $duration = $start->diffInDays($end) + 1;
                                    @endphp
                                    {{ $duration }} day{{ $duration > 1 ? 's' : '' }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6>Employee Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $training->employee->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Employee ID:</strong></td>
                                <td>{{ $training->employee->id_employees }}</td>
                            </tr>
                            <tr>
                                <td><strong>Position:</strong></td>
                                <td>{{ $training->employee->position ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($training->description)
                <div class="row mt-4">
                    <div class="col-12">
                        <h6>Description</h6>
                        <div class="card">
                            <div class="card-body">
                                <p class="mb-0">{{ $training->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row mt-4">
                    <div class="col-12">
                        <h6>System Information</h6>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td><strong>Created:</strong></td>
                                <td>{{ $training->created_at->format('d F Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Last Updated:</strong></td>
                                <td>{{ $training->updated_at->format('d F Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
