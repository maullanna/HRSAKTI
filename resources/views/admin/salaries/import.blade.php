@extends('layouts.master')

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Import Salaries</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('salaries.index') }}">Salaries</a></li>
        <li class="breadcrumb-item active">Import</li>
    </ol>
</div>
@endsection

@section('content')
@include('includes.flash')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Import Data Gaji</h5>
                <p class="card-text">Upload file Excel (XLSX) untuk mengimpor data gaji karyawan.</p>
            </div>
            <div class="card-body">
                <form action="{{ route('salaries.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="import_file">Pilih File <span class="text-danger">*</span></label>
                                <input type="file" name="import_file" id="import_file"
                                    class="form-control @error('import_file') is-invalid @enderror"
                                    accept=".xlsx" required>
                                <small class="form-text text-muted">
                                    Format yang didukung: XLSX saja (Maksimal 2MB)
                                </small>
                                @error('import_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <a href="{{ route('salaries.download-template') }}"
                                        class="btn btn-info btn-sm">
                                        <i class="mdi mdi-download mr-1"></i>Download Template
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" id="importBtn">
                            <i class="mdi mdi-upload mr-2"></i>Import Data
                        </button>
                        <a href="{{ route('salaries.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left mr-2"></i>Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Panduan Import</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Format File yang Didukung:</h6>
                        <ul>
                            <li><strong>XLSX</strong> - Excel 2007+ (Format yang digunakan)</li>
                        </ul>

                        <h6>Kolom yang Diperlukan:</h6>
                        <ol>
                            <li><strong>employee_id</strong> - ID karyawan (harus ada di database)</li>
                            <li><strong>month</strong> - Bulan dalam format YYYY-MM (contoh: 2024-01)</li>
                            <li><strong>basic_salary</strong> - Gaji pokok (angka positif)</li>
                            <li><strong>allowances</strong> - Tunjangan (opsional, format JSON)</li>
                            <li><strong>deductions</strong> - Potongan (opsional, format JSON)</li>
                        </ol>
                    </div>

                    <div class="col-md-6">
                        <h6>Contoh Format Allowances & Deductions:</h6>
                        <div class="alert alert-info">
                            <strong>JSON Format:</strong><br>
                            <code>{"transport": 500000, "meal": 300000}</code><br><br>

                            <strong>Key:Value Format:</strong><br>
                            <code>transport: 500000; meal: 300000</code>
                        </div>

                        <h6>Contoh Data:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>employee_id</th>
                                        <th>month</th>
                                        <th>basic_salary</th>
                                        <th>allowances</th>
                                        <th>deductions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>2024-01</td>
                                        <td>5000000</td>
                                        <td>{"transport": 500000}</td>
                                        <td>{"tax": 500000}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('importForm');
        const importBtn = document.getElementById('importBtn');
        const fileInput = document.getElementById('import_file');

        form.addEventListener('submit', function(e) {
            if (!fileInput.files.length) {
                e.preventDefault();
                alert('Pilih file terlebih dahulu!');
                return;
            }

            // Show loading state
            importBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin mr-2"></i>Mengimport...';
            importBtn.disabled = true;
        });

        // File validation
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Check file size (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    this.value = '';
                    return;
                }

                // Check file extension
                const allowedExtensions = ['xlsx'];
                const fileExtension = file.name.split('.').pop().toLowerCase();

                if (!allowedExtensions.includes(fileExtension)) {
                    alert('Format file tidak didukung. Gunakan XLSX saja.');
                    this.value = '';
                    return;
                }
            }
        });
    });
</script>

@endsection