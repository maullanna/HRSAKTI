@extends('layouts.master')

@section('css')
<style>
/* Fix untuk select box yang teksnya terpotong */
select.form-control {
    height: 38px !important;
    min-height: 38px !important;
    padding: 0.5rem 2rem 0.5rem 0.75rem !important;
    line-height: 1.5 !important;
    font-size: 14px !important;
    vertical-align: middle !important;
}

select.form-control:focus {
    height: 38px !important;
    min-height: 38px !important;
    padding: 0.5rem 2rem 0.5rem 0.75rem !important;
    line-height: 1.5 !important;
}

select.form-control option {
    padding: 10px 12px !important;
    line-height: 1.8 !important;
    min-height: 30px !important;
}

/* Pastikan semua select box di modal dan card memiliki styling yang benar */
.modal-body select.form-control,
.card-body select.form-control,
.form-group select.form-control {
    height: 38px !important;
    min-height: 38px !important;
    padding: 0.5rem 2rem 0.5rem 0.75rem !important;
    line-height: 1.5 !important;
    vertical-align: middle !important;
    display: block !important;
}
</style>
@endsection

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Employees</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Employees</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0);">Employees List</a></li>
  
    </ol>
</div>
@endsection
@section('button')
<a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat"><i class="mdi mdi-plus mr-2"></i>Add</a>
        

@endsection

@section('content')
@include('includes.flash')

<!-- Contract Expiring Alert -->
@php
    $expiringContracts = $employees->filter(function($emp) {
        return $emp->isContractExpiringSoon();
    });
    $expiredContracts = $employees->filter(function($emp) {
        return $emp->isContractExpired() && $emp->status == 'active';
    });
@endphp

@if($expiringContracts->count() > 0 || $expiredContracts->count() > 0)
<div class="row mb-3">
    <div class="col-12">
        @if($expiredContracts->count() > 0)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5><i class="fa fa-exclamation-triangle"></i> <strong>Peringatan!</strong> Ada {{ $expiredContracts->count() }} karyawan yang kontraknya sudah berakhir:</h5>
            <ul class="mb-0">
                @foreach($expiredContracts as $emp)
                <li>
                    <strong>{{ $emp->name }}</strong> ({{ $emp->kontrak_kerja }}) - 
                    Berakhir: {{ $emp->contract_end_date->format('d/m/Y') }}
                    - <a href="#edit{{$emp->id_employees}}" data-toggle="modal" class="alert-link">Edit Sekarang</a>
                </li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        
        @if($expiringContracts->count() > 0)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <h5><i class="fa fa-bell"></i> <strong>Notifikasi!</strong> Ada {{ $expiringContracts->count() }} karyawan yang kontraknya akan berakhir dalam 1 bulan:</h5>
            <ul class="mb-0">
                @foreach($expiringContracts as $emp)
                @php $daysLeft = $emp->getDaysUntilContractExpires(); @endphp
                <li>
                    <strong>{{ $emp->name }}</strong> ({{ $emp->kontrak_kerja }}) - 
                    Berakhir: {{ $emp->contract_end_date->format('d/m/Y') }} 
                    (<strong>{{ $daysLeft }} hari lagi</strong>)
                    - <a href="#edit{{$emp->id_employees}}" data-toggle="modal" class="alert-link">Review Kontrak</a>
                </li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
    </div>
</div>
@endif

<!--Show Validation Errors here-->
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<!--End showing Validation Errors here-->


                      <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        
                                                    <thead>
                                                    <tr>
                                                        <th data-priority="1">Employee ID</th>
                                                        <th data-priority="2">Name</th>
                                                        <th data-priority="3">NIK</th>
                                                        <th data-priority="4">Tanggal Lahir</th>
                                                        <th data-priority="5">Pendidikan</th>
                                                        <th data-priority="6">Kontrak Kerja</th>
                                                        <th data-priority="7">Position</th>
                                                        <th data-priority="8">Email</th>
                                                        <th data-priority="9">Phone</th>
                                                        <th data-priority="11">Tanggal Mulai Kontrak</th>
                                                        <th data-priority="10">Status</th>
                                                        <th data-priority="12">Akhir Kontrak</th>
                                                        <th data-priority="13">Actions</th>
                                                     
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach( $employees as $employee)
                                                        @php
                                                            $daysLeft = $employee->getDaysUntilContractExpires();
                                                            $isExpiring = $employee->isContractExpiringSoon();
                                                            $isExpired = $employee->isContractExpired();
                                                        @endphp
                                                        <tr class="{{ $isExpired ? 'table-danger' : ($isExpiring ? 'table-warning' : '') }}">
                                                            <td>{{$employee->id_employees}}</td>
                                                            <td>{{$employee->name}}</td>
                                                            <td>{{$employee->nik ?? '-'}}</td>
                                                            <td>{{$employee->tanggal_lahir ? \Carbon\Carbon::parse($employee->tanggal_lahir)->format('d/m/Y') : '-'}}</td>
                                                            <td>{{$employee->pendidikan ?? '-'}}</td>
                                                            <td>{{$employee->kontrak_kerja ?? '-'}}</td>
                                                            <td>{{$employee->position}}</td>
                                                            <td>{{$employee->email}}</td>
                                                            <td>{{$employee->phone ?? '-'}}</td>
                                                            <td>
                                                                {{$employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('d/m/Y') : '-'}}
                                                            </td>
                                                            <td>
                                                                @if($employee->status == 'active')
                                                                    <span class="badge badge-success">Active</span>
                                                                @else
                                                                    <span class="badge badge-danger">Inactive</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($employee->contract_end_date)
                                                                    @if($daysLeft < 0)
                                                                        <span class="badge badge-danger" title="Kontrak sudah berakhir">
                                                                            <i class="fa fa-exclamation-triangle"></i> Berakhir: {{$employee->contract_end_date->format('d/m/Y')}}
                                                                        </span>
                                                                    @elseif($daysLeft <= 30)
                                                                        <span class="badge badge-warning" title="Kontrak akan berakhir dalam {{$daysLeft}} hari">
                                                                            <i class="fa fa-clock"></i> {{$employee->contract_end_date->format('d/m/Y')}} ({{$daysLeft}} hari lagi)
                                                                        </span>
                                                                    @else
                                                                        <span class="badge badge-info">{{$employee->contract_end_date->format('d/m/Y')}}</span>
                                                                    @endif
                                                                @else
                                                                    <span class="badge badge-secondary">-</span>
                                                                @endif
                                                            </td>
                                                            <td>
                        
                                                                <a href="#edit{{$employee->id_employees}}" data-toggle="modal" class="btn btn-success btn-sm edit btn-flat"><i class='fa fa-edit'></i> Edit</a>
                                                                <a href="#delete{{$employee->id_employees}}" data-toggle="modal" class="btn btn-danger btn-sm delete btn-flat"><i class='fa fa-trash'></i> Delete</a>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                   
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div> <!-- end row -->    
                                    

@foreach( $employees as $employee)
@include('admin.master-data.employees.edit_delete_employee')
@endforeach

@include('admin.master-data.employees.add_employee')

@endsection


@section('script')
<!-- Responsive-table-->

<script>
// Function untuk toggle kontrak durasi di form edit (dynamic untuk setiap employee)
@foreach($employees as $employee)
function toggleDurasiKontrakEdit{{ $employee->id_employees }}() {
    var kontrakKerja = document.getElementById('kontrak_kerja{{ $employee->id_employees }}').value;
    var durasiGroup = document.getElementById('durasi_kontrak_group{{ $employee->id_employees }}');
    var hireDateGroup = document.getElementById('hire_date_group{{ $employee->id_employees }}');
    var durasiInput = document.getElementById('kontrak_durasi{{ $employee->id_employees }}');
    var hireDateInput = document.getElementById('hire_date{{ $employee->id_employees }}');
    var hireDateTetapInput = document.getElementById('hire_date_tetap{{ $employee->id_employees }}');
    
    var perluDurasi = ['Magang', 'Kontrak', 'PKL', 'Freelance'];
    
    if (perluDurasi.includes(kontrakKerja)) {
        durasiGroup.style.display = 'flex';
        hireDateGroup.style.display = 'none';
        durasiInput.setAttribute('required', 'required');
        hireDateInput.setAttribute('required', 'required');
        hireDateTetapInput.removeAttribute('required');
        // Sync value
        if (hireDateTetapInput && hireDateTetapInput.value) {
            hireDateInput.value = hireDateTetapInput.value;
        }
    } else {
        durasiGroup.style.display = 'none';
        hireDateGroup.style.display = 'flex';
        durasiInput.removeAttribute('required');
        if (kontrakKerja !== '{{ $employee->kontrak_kerja }}') {
            durasiInput.value = '';
        }
        hireDateInput.removeAttribute('required');
        if (hireDateTetapInput) {
            hireDateTetapInput.setAttribute('required', 'required');
            // Sync value
            if (hireDateInput.value) {
                hireDateTetapInput.value = hireDateInput.value;
            }
        }
    }
}

// Sync hire_date inputs for edit form {{ $employee->id_employees }}
(function() {
    var hireDateInput = document.getElementById('hire_date{{ $employee->id_employees }}');
    var hireDateTetapInput = document.getElementById('hire_date_tetap{{ $employee->id_employees }}');
    
    if (hireDateInput && hireDateTetapInput) {
        hireDateInput.addEventListener('change', function() {
            hireDateTetapInput.value = this.value;
        });
        
        hireDateTetapInput.addEventListener('change', function() {
            hireDateInput.value = this.value;
        });
    }
})();
@endforeach
</script>

@endsection