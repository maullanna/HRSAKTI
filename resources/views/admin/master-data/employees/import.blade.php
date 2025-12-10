@extends('layouts.master')

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Import Employees</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
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
                <h5 class="card-title">Import Data Employee</h5>
                <p class="card-text">Upload file Excel atau CSV untuk mengimpor data karyawan.</p>
            </div>
            <div class="card-body">
                <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="import_file">Pilih File <span class="text-danger">*</span></label>
                                <input type="file" name="import_file" id="import_file"
                                    class="form-control @error('import_file') is-invalid @enderror"
                                    accept=".xlsx" required>
                                <small class="form-text text-muted">
                                    Format yang didukung: XLSX saja (Maksimal 5MB)
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
                                    <a href="{{ route('employees.download-template') }}"
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
                        <a href="{{ route('employees.index') }}" class="btn btn-secondary">
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

                        <h6 class="mt-3">Kolom Wajib:</h6>
                        <ol>
                            <li><strong>employee_code</strong> - Kode karyawan (unik, tidak boleh duplikat)</li>
                            <li><strong>name</strong> - Nama lengkap karyawan</li>
                            <li><strong>position</strong> - Posisi/jabatan</li>
                        </ol>

                        <h6 class="mt-3">Kolom Opsional:</h6>
                        <ul>
                            <li><strong>nik</strong> - Nomor Induk Kependudukan</li>
                            <li><strong>tanggal_lahir</strong> - Format: YYYY-MM-DD atau DD/MM/YYYY</li>
                            <li><strong>pendidikan</strong> - SD, SMP, SMA/SMK, D1, D2, D3, D4, S1, S2, S3</li>
                            <li><strong>kontrak_kerja</strong> - Tetap, Kontrak, Magang, PKL, Freelance</li>
                            <li><strong>kontrak_durasi</strong> - Durasi kontrak dalam bulan (wajib jika kontrak_kerja bukan Tetap)</li>
                            <li><strong>email</strong> - Email karyawan (unik)</li>
                            <li><strong>phone</strong> - Nomor telepon</li>
                            <li><strong>hire_date</strong> - Tanggal mulai kerja (Format: YYYY-MM-DD atau DD/MM/YYYY)</li>
                            <li><strong>status</strong> - active atau inactive (default: active)</li>
                            <li><strong>pin_code</strong> - PIN untuk login (minimal 4 karakter)</li>
                        </ul>
                    </div>

                    <div class="col-md-6">
                        <h6>Posisi yang Valid:</h6>
                        <div class="alert alert-info">
                            Wadir 1, Wadir 2, Section Prodi TPMO, Section Prodi TOPKR4, Section BAAK, Section Teaching Factory, Section IT & Sarpras, Section Administrasi, YTI Board of Directors, Employees, Magang, PKL
                        </div>

                        <h6>Contoh Format Data:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>employee_code</th>
                                        <th>name</th>
                                        <th>nik</th>
                                        <th>tanggal_lahir</th>
                                        <th>pendidikan</th>
                                        <th>kontrak_kerja</th>
                                        <th>position</th>
                                        <th>email</th>
                                        <th>phone</th>
                                        <th>hire_date</th>
                                        <th>status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>EMP001</td>
                                        <td>John Doe</td>
                                        <td>3201234567890001</td>
                                        <td>1990-01-15</td>
                                        <td>S1</td>
                                        <td>Tetap</td>
                                        <td>Employees</td>
                                        <td>john@example.com</td>
                                        <td>081234567890</td>
                                        <td>2024-01-01</td>
                                        <td>active</td>
                                    </tr>
                                    <tr>
                                        <td>EMP002</td>
                                        <td>Jane Smith</td>
                                        <td>3201234567890002</td>
                                        <td>1992-05-20</td>
                                        <td>S1</td>
                                        <td>Kontrak</td>
                                        <td>Magang</td>
                                        <td>jane@example.com</td>
                                        <td>081234567891</td>
                                        <td>2024-02-01</td>
                                        <td>active</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-warning mt-3">
                            <strong>Catatan Penting:</strong>
                            <ul class="mb-0">
                                <li>Employee Code harus unik dan tidak boleh duplikat</li>
                                <li>Email harus unik jika diisi</li>
                                <li>Format tanggal: YYYY-MM-DD atau DD/MM/YYYY</li>
                                <li>Jika kontrak_kerja bukan "Tetap", kontrak_durasi wajib diisi</li>
                            </ul>
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
                // Check file size (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar. Maksimal 5MB.');
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