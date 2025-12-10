@extends('layouts.master')

@section('css')
<!-- Table css -->
<link href="{{ URL::asset('plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css') }}" rel="stylesheet"
   type="text/css" media="screen">
@endsection

@section('breadcrumb')
<div class="col-sm-6">
   <h4 class="page-title text-left">Overtime Reports</h4>
   <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
      <li class="breadcrumb-item"><a href="javascript:void(0);">Overtime</a></li>
      <li class="breadcrumb-item"><a href="javascript:void(0);">Reports</a></li>
   </ol>
</div>
@endsection

@section('button')
<a href="{{ route('overtime.reports.create') }}" class="btn btn-primary btn-sm btn-flat">
   <i class="mdi mdi-plus mr-2"></i>Add Overtime
</a>
<a href="/leave" class="btn btn-secondary btn-sm btn-flat ml-2">
   <i class="mdi mdi-table mr-2"></i>Leave Table
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
                           <th data-priority="1">Date</th>
                           <th data-priority="2">Employee ID</th>
                           <th data-priority="3">Name</th>
                           <th data-priority="4">Start Time</th>
                           <th data-priority="5">End Time</th>
                           <th data-priority="6">Duration</th>
                           <th data-priority="7">Status</th>
                           <th data-priority="8">Actions</th>
                        </tr>
                     </thead>
                     <tbody>
                        @foreach ($overtimes as $overtime)
                        <tr>
                           <td>{{ $overtime->overtime_date }}</td>
                           <td>{{ $overtime->emp_id }}</td>
                           <td>{{ $overtime->employee->name ?? 'N/A' }}</td>
                           <td>{{ $overtime->start_time ? \Carbon\Carbon::parse($overtime->start_time)->format('H:i') : 'N/A' }}</td>
                           <td>{{ $overtime->end_time ? \Carbon\Carbon::parse($overtime->end_time)->format('H:i') : 'N/A' }}</td>
                           <td>{{ $overtime->duration ?? 'N/A' }}</td>
                           <td>
                              <span class="badge badge-{{ $overtime->status == 'approved' ? 'success' : ($overtime->status == 'rejected' ? 'danger' : 'warning') }}">
                                 {{ ucfirst($overtime->status) }}
                              </span>
                           </td>
                           <td>
                              <div class="action-buttons">
                                 <a href="{{ route('overtime.reports.show', ['overtime' => $overtime, 'from' => 'reports']) }}" class="btn btn-info" title="View Details" data-toggle="tooltip" data-placement="top">
                                    <i class="mdi mdi-eye-outline"></i>
                                 </a>
                                 @php
                                 // Check if user can edit/delete
                                 // Only super_admin and SDM/HRD can edit/delete
                                 $isEmployee = Auth::guard('employee')->check();
                                 $user = Auth::guard('employee')->check() ? Auth::guard('employee')->user() : Auth::guard('web')->user();

                                 $canEditDelete = false;

                                 if ($isEmployee) {
                                 // Employee: check position
                                 $position = $user->position ?? '';
                                 // Only SDM/HRD position can edit/delete
                                 // Employees, Magang, PKL cannot edit/delete
                                 if ($position === 'SDM/HRD') {
                                 $canEditDelete = true;
                                 }
                                 } else {
                                 // Admin: check roles
                                 if ($user) {
                                 $userRoles = $user->roles()->pluck('slug')->toArray();
                                 // Only super_admin and admin_sdm can edit/delete
                                 $canEditDelete = in_array('super_admin', $userRoles) || in_array('admin_sdm', $userRoles);
                                 }
                                 }
                                 @endphp
                                 @if($canEditDelete)
                                 <a href="{{ route('overtime.reports.edit', $overtime) }}" class="btn btn-warning" title="Edit" data-toggle="tooltip" data-placement="top">
                                    <i class="mdi mdi-pencil-outline"></i>
                                 </a>
                                 <form action="{{ route('overtime.reports.destroy', $overtime) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="Delete" data-toggle="tooltip" data-placement="top" onclick="return confirm('Are you sure you want to delete this overtime request?')">
                                       <i class="mdi mdi-delete-outline"></i>
                                    </button>
                                 </form>
                                 @endif
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
@endsection

@section('script')
<script>
   $(document).ready(function() {
      // Initialize tooltips
      $('[data-toggle="tooltip"]').tooltip();
   });
</script>
@endsection