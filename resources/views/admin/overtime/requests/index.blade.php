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
<a href="{{ route('overtime.requests.create') }}" class="btn btn-primary btn-sm btn-flat">
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
                        <td>{{ $overtime->id_overtime }}</td>
                        <td>{{ $overtime->employee->name ?? 'N/A' }}</td>
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
                        <td>
                           <span class="badge badge-{{ $overtime->status == 'approved' ? 'success' : ($overtime->status == 'rejected' ? 'danger' : 'warning') }}">
                              {{ ucfirst($overtime->status) }}
                           </span>
                        </td>
                        <td>
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
                           <div class="action-buttons">
                              <a href="{{ route('overtime.show', ['overtime' => $overtime, 'from' => 'requests']) }}" class="btn btn-info" title="View Details" data-toggle="tooltip" data-placement="top">
                                 <i class="mdi mdi-eye-outline"></i>
                              </a>
                              @if($canEditDelete)
                              <a href="{{ route('overtime.edit', $overtime) }}" class="btn btn-warning" title="Edit" data-toggle="tooltip" data-placement="top">
                                 <i class="mdi mdi-pencil-outline"></i>
                              </a>
                              <form action="{{ route('overtime.destroy', $overtime) }}" method="POST" class="d-inline">
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