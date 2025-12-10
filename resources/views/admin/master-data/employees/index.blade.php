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
<a href="#importEmployee" data-toggle="modal" class="btn btn-success btn-sm btn-flat"><i class="mdi mdi-upload mr-2"></i>Import</a>

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
                            <td>{{$employee->employee_code ?? '-'}}</td>
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

<!-- Import Employee Modal -->
<div class="modal fade" id="importEmployee">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h4 class="modal-title"><b><i class="mdi mdi-upload mr-2"></i>Import Employee</b></h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 25px; max-height: 70vh; overflow-y: auto;">
                <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data" id="importEmployeeForm">
                    @csrf

                    <div class="alert alert-info">
                        <i class="mdi mdi-information mr-2"></i>
                        <strong>Format yang didukung:</strong> CSV, XLSX, XLS (Maksimal 5MB)<br>
                        <small><i class="mdi mdi-lightbulb-on mr-1"></i><strong>Tips:</strong> Download template Excel untuk format yang lebih rapi dengan styling dan contoh data!</small>
                    </div>

                    <div class="form-group">
                        <label for="import_file">Pilih File <span class="text-danger">*</span></label>
                        <input type="file" name="import_file" id="import_file"
                            class="form-control @error('import_file') is-invalid @enderror"
                            accept=".csv,.xlsx,.xls" required>
                        <small class="form-text text-muted">
                            Format yang didukung: CSV, XLSX, XLS (Maksimal 5MB)
                        </small>
                        @error('import_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <a href="{{ route('employees.download-template') }}"
                            class="btn btn-info btn-sm" id="downloadTemplateBtn" target="_blank">
                            <i class="mdi mdi-download mr-1"></i>Download Template Excel
                        </a>
                        <button type="button" class="btn btn-secondary btn-sm" data-toggle="collapse" data-target="#exampleTable">
                            <i class="mdi mdi-eye mr-1"></i>Lihat Contoh Data
                        </button>
                    </div>

                    <!-- Contoh Data Table -->
                    <div class="collapse mt-3" id="exampleTable">
                        <div class="card card-body" style="background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <h6 class="mb-4" style="color: #495057; font-weight: 700; font-size: 16px; border-bottom: 2px solid #667eea; padding-bottom: 10px;">
                                <i class="mdi mdi-table-large mr-2" style="color: #667eea;"></i>Contoh Format Data:
                            </h6>
                            <div class="table-responsive" style="border-radius: 6px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                <table class="table table-bordered table-hover mb-0" style="margin-bottom: 0 !important; border-collapse: separate; border-spacing: 0; background-color: white;">
                                    <thead>
                                        <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            <th style="padding: 16px 14px; font-weight: 700; white-space: nowrap; min-width: 130px; border-right: 1px solid rgba(255,255,255,0.2); font-size: 13px; letter-spacing: 0.5px;">Employee Code</th>
                                            <th style="padding: 16px 14px; font-weight: 700; white-space: nowrap; min-width: 150px; border-right: 1px solid rgba(255,255,255,0.2); font-size: 13px; letter-spacing: 0.5px;">Name</th>
                                            <th style="padding: 16px 14px; font-weight: 700; white-space: nowrap; min-width: 160px; border-right: 1px solid rgba(255,255,255,0.2); font-size: 13px; letter-spacing: 0.5px;">NIK</th>
                                            <th style="padding: 16px 14px; font-weight: 700; white-space: nowrap; min-width: 140px; border-right: 1px solid rgba(255,255,255,0.2); font-size: 13px; letter-spacing: 0.5px;">Tanggal Lahir</th>
                                            <th style="padding: 16px 14px; font-weight: 700; white-space: nowrap; min-width: 120px; border-right: 1px solid rgba(255,255,255,0.2); font-size: 13px; letter-spacing: 0.5px;">Pendidikan</th>
                                            <th style="padding: 16px 14px; font-weight: 700; white-space: nowrap; min-width: 110px; border-right: 1px solid rgba(255,255,255,0.2); font-size: 13px; letter-spacing: 0.5px;">Kontrak</th>
                                            <th style="padding: 16px 14px; font-weight: 700; white-space: nowrap; min-width: 130px; border-right: 1px solid rgba(255,255,255,0.2); font-size: 13px; letter-spacing: 0.5px;">Position</th>
                                            <th style="padding: 16px 14px; font-weight: 700; white-space: nowrap; min-width: 190px; border-right: 1px solid rgba(255,255,255,0.2); font-size: 13px; letter-spacing: 0.5px;">Email</th>
                                            <th style="padding: 16px 14px; font-weight: 700; white-space: nowrap; min-width: 140px; border-right: 1px solid rgba(255,255,255,0.2); font-size: 13px; letter-spacing: 0.5px;">Phone</th>
                                            <th style="padding: 16px 14px; font-weight: 700; white-space: nowrap; min-width: 130px; border-right: 1px solid rgba(255,255,255,0.2); font-size: 13px; letter-spacing: 0.5px;">Hire Date</th>
                                            <th style="padding: 16px 14px; font-weight: 700; white-space: nowrap; min-width: 100px; font-size: 13px; letter-spacing: 0.5px;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr style="background-color: #ffffff; transition: background-color 0.2s;">
                                            <td style="padding: 14px 14px; font-family: 'Courier New', monospace; font-weight: 700; color: #28a745; font-size: 14px; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">EMP001</td>
                                            <td style="padding: 14px 14px; font-size: 14px; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; color: #495057;">John Doe</td>
                                            <td style="padding: 14px 14px; font-family: 'Courier New', monospace; font-size: 13px; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; color: #495057;">3201234567890001</td>
                                            <td style="padding: 14px 14px; font-family: 'Courier New', monospace; font-size: 13px; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; color: #495057;">1990-01-15</td>
                                            <td style="padding: 14px 14px; font-size: 14px; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; color: #495057; font-weight: 500;">S1</td>
                                            <td style="padding: 14px 14px; font-size: 14px; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; color: #495057;">Tetap</td>
                                            <td style="padding: 14px 14px; font-size: 14px; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; color: #495057;">Employees</td>
                                            <td style="padding: 14px 14px; font-size: 12px; word-break: break-all; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; color: #495057;">john.doe@example.com</td>
                                            <td style="padding: 14px 14px; font-family: 'Courier New', monospace; font-size: 13px; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; color: #495057;">081234567890</td>
                                            <td style="padding: 14px 14px; font-family: 'Courier New', monospace; font-size: 13px; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; color: #495057;">2024-01-01</td>
                                            <td style="padding: 14px 14px; border-bottom: 1px solid #dee2e6;"><span class="badge badge-success" style="font-size: 11px; padding: 5px 10px; font-weight: 600;">active</span></td>
                                        </tr>
                                        <tr style="background-color: #f8f9fa; transition: background-color 0.2s;">
                                            <td style="padding: 14px 14px; font-family: 'Courier New', monospace; font-weight: 700; color: #28a745; font-size: 14px; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">EMP002</td>
                                            <td style="padding: 14px 14px; font-size: 14px; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; color: #495057;">Jane Smith</td>
                                            <td style="padding: 14px 14px; font-family: 'Courier New', monospace; font-size: 13px; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; color: #495057;">3201234567890002</td>
                                            <td style="padding: 14px 14px; font-family: 'Courier New', monospace; font-size: 13px; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; color: #495057;">1992-05-20</td>
                                            <td style="padding: 14px 14px; font-size: 14px; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; color: #495057; font-weight: 500;">S1</td>
                                            <td style="padding: 14px 14px; font-size: 14px; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; color: #495057;">Kontrak</td>
                                            <td style="padding: 14px 14px; font-size: 14px; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; color: #495057;">Magang</td>
                                            <td style="padding: 14px 14px; font-size: 12px; word-break: break-all; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; color: #495057;">jane.smith@example.com</td>
                                            <td style="padding: 14px 14px; font-family: 'Courier New', monospace; font-size: 13px; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; color: #495057;">081234567891</td>
                                            <td style="padding: 14px 14px; font-family: 'Courier New', monospace; font-size: 13px; border-right: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; color: #495057;">2024-02-01</td>
                                            <td style="padding: 14px 14px; border-bottom: 1px solid #dee2e6;"><span class="badge badge-success" style="font-size: 11px; padding: 5px 10px; font-weight: 600;">active</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="alert alert-info mt-3 mb-0" style="border-left: 4px solid #17a2b8;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong style="color: #0c5460;"><i class="mdi mdi-alert-circle mr-1"></i>Kolom Wajib:</strong>
                                        <ul class="mb-2 mt-1" style="font-size: 13px;">
                                            <li><code style="background: #e9ecef; padding: 2px 6px; border-radius: 3px;">employee_code</code></li>
                                            <li><code style="background: #e9ecef; padding: 2px 6px; border-radius: 3px;">name</code></li>
                                            <li><code style="background: #e9ecef; padding: 2px 6px; border-radius: 3px;">position</code></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <strong style="color: #0c5460;"><i class="mdi mdi-information mr-1"></i>Kolom Opsional:</strong>
                                        <ul class="mb-2 mt-1" style="font-size: 13px;">
                                            <li>nik, tanggal_lahir, pendidikan, kontrak_kerja</li>
                                            <li>kontrak_durasi, email, phone, hire_date</li>
                                            <li>status, pin_code</li>
                                        </ul>
                                    </div>
                                </div>
                                <hr style="margin: 10px 0; border-color: #bee5eb;">
                                <div style="font-size: 13px;">
                                    <strong style="color: #0c5460;"><i class="mdi mdi-calendar-clock mr-1"></i>Format Tanggal:</strong>
                                    <code style="background: #e9ecef; padding: 2px 6px; border-radius: 3px;">YYYY-MM-DD</code> atau
                                    <code style="background: #e9ecef; padding: 2px 6px; border-radius: 3px;">DD/MM/YYYY</code><br>
                                    <strong style="color: #0c5460;"><i class="mdi mdi-briefcase mr-1"></i>Posisi Valid:</strong>
                                    Wadir 1, Wadir 2, Section Prodi TPMO, Section Prodi TOPKR4, Section BAAK, Section Teaching Factory, Section IT & Sarpras, Section Administrasi, YTI Board of Directors, Employees, Magang, PKL
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-success" id="importEmployeeBtn">
                            <i class="mdi mdi-upload mr-2"></i>Import Data
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="mdi mdi-close mr-2"></i>Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection


@section('script')
<!-- Responsive-table-->

<script>
    // Import Employee Form Handler
    document.addEventListener('DOMContentLoaded', function() {
        const importForm = document.getElementById('importEmployeeForm');
        const importBtn = document.getElementById('importEmployeeBtn');
        const fileInput = document.getElementById('import_file');

        if (importForm && fileInput) {
            // File validation
            fileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    // Check file size (5MB max)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('Ukuran file terlalu besar. Maksimal 5MB.');
                        this.value = '';
                        return;
                    }

                    // Check file extension
                    const allowedExtensions = ['csv', 'xlsx', 'xls'];
                    const fileExtension = file.name.split('.').pop().toLowerCase();

                    if (!allowedExtensions.includes(fileExtension)) {
                        alert('Format file tidak didukung. Gunakan CSV, XLSX, atau XLS.');
                        this.value = '';
                        return;
                    }
                }
            });

            // Form submission
            importForm.addEventListener('submit', function(e) {
                if (!fileInput.files.length) {
                    e.preventDefault();
                    alert('Pilih file terlebih dahulu!');
                    return;
                }

                // Show loading state
                if (importBtn) {
                    importBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin mr-2"></i>Mengimport...';
                    importBtn.disabled = true;
                }
            });
        }
    });

    // Function untuk toggle kontrak durasi di form edit (dynamic untuk setiap employee)
    // Menggunakan event delegation untuk menghindari error decorator
    document.addEventListener('DOMContentLoaded', function() {
        // Event delegation untuk semua kontrak_kerja select
        document.addEventListener('change', function(e) {
            if (e.target && e.target.id && e.target.id.startsWith('kontrak_kerja')) {
                var empId = e.target.id.replace('kontrak_kerja', '');
                var kontrakKerja = e.target.value;
                var durasiGroup = document.getElementById('durasi_kontrak_group' + empId);
                var hireDateGroup = document.getElementById('hire_date_group' + empId);
                var durasiInput = document.getElementById('kontrak_durasi' + empId);
                var hireDateInput = document.getElementById('hire_date' + empId);
                var hireDateTetapInput = document.getElementById('hire_date_tetap' + empId);

                if (!durasiGroup || !hireDateGroup) return;

                var perluDurasi = ['Magang', 'Kontrak', 'PKL', 'Freelance'];

                if (perluDurasi.includes(kontrakKerja)) {
                    durasiGroup.style.display = 'flex';
                    hireDateGroup.style.display = 'none';
                    if (durasiInput) durasiInput.setAttribute('required', 'required');
                    if (hireDateInput) hireDateInput.setAttribute('required', 'required');
                    if (hireDateTetapInput) hireDateTetapInput.removeAttribute('required');
                    // Sync value
                    if (hireDateTetapInput && hireDateTetapInput.value && hireDateInput) {
                        hireDateInput.value = hireDateTetapInput.value;
                    }
                } else {
                    durasiGroup.style.display = 'none';
                    hireDateGroup.style.display = 'flex';
                    if (durasiInput) durasiInput.removeAttribute('required');
                    if (durasiInput) durasiInput.value = '';
                    if (hireDateInput) hireDateInput.removeAttribute('required');
                    if (hireDateTetapInput) {
                        hireDateTetapInput.setAttribute('required', 'required');
                        // Sync value
                        if (hireDateInput && hireDateInput.value) {
                            hireDateTetapInput.value = hireDateInput.value;
                        }
                    }
                }
            }
        });

        // Event delegation untuk sync hire_date inputs
        document.addEventListener('change', function(e) {
            if (e.target && e.target.id) {
                var empId = '';
                var hireDateInput = null;
                var hireDateTetapInput = null;

                if (e.target.id.startsWith('hire_date') && !e.target.id.includes('_tetap')) {
                    empId = e.target.id.replace('hire_date', '');
                    hireDateInput = e.target;
                    hireDateTetapInput = document.getElementById('hire_date_tetap' + empId);
                } else if (e.target.id.startsWith('hire_date_tetap')) {
                    empId = e.target.id.replace('hire_date_tetap', '');
                    hireDateTetapInput = e.target;
                    hireDateInput = document.getElementById('hire_date' + empId);
                }

                if (hireDateInput && hireDateTetapInput) {
                    if (e.target === hireDateInput) {
                        hireDateTetapInput.value = hireDateInput.value;
                    } else if (e.target === hireDateTetapInput) {
                        hireDateInput.value = hireDateTetapInput.value;
                    }
                }
            }
        });
    });
</script>

@endsection