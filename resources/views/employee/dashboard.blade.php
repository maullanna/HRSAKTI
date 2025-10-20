@extends('layouts.master')

@section('css')
@endsection

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Employee Dashboard</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Employee</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
</div>
@endsection

@section('button')
<div class="col-sm-6">
    <div class="text-right">
        <span class="text-muted">Welcome, {{ $employee->name }}</span>
    </div>
</div>
@endsection

@section('content')
@include('includes.flash')

<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-1 overflow-hidden">
                        <p class="text-truncate font-size-14 mb-2">Employee ID</p>
                        <h4 class="mb-0">{{ $employee->id }}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle bg-primary bg-soft">
                            <span class="avatar-title rounded-circle bg-primary">
                                <i class="mdi mdi-account font-size-18"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-1 overflow-hidden">
                        <p class="text-truncate font-size-14 mb-2">Name</p>
                        <h4 class="mb-0">{{ $employee->name }}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle bg-success bg-soft">
                            <span class="avatar-title rounded-circle bg-success">
                                <i class="mdi mdi-account-circle font-size-18"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-1 overflow-hidden">
                        <p class="text-truncate font-size-14 mb-2">Position</p>
                        <h4 class="mb-0">{{ $employee->position }}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle bg-info bg-soft">
                            <span class="avatar-title rounded-circle bg-info">
                                <i class="mdi mdi-briefcase font-size-18"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-1 overflow-hidden">
                        <p class="text-truncate font-size-14 mb-2">Email</p>
                        <h4 class="mb-0">{{ $employee->email ?: 'Not provided' }}</h4>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded-circle bg-warning bg-soft">
                            <span class="avatar-title rounded-circle bg-warning">
                                <i class="mdi mdi-email font-size-18"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Employee Information</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td><strong>Employee ID</strong></td>
                                <td>{{ $employee->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Name</strong></td>
                                <td>{{ $employee->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Position</strong></td>
                                <td>{{ $employee->position }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email</strong></td>
                                <td>{{ $employee->email ?: 'Not provided' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Member Since</strong></td>
                                <td>{{ $employee->created_at->format('M d, Y') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
@endsection
