<!-- Add -->
<div class="modal fade" id="addnew">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title"><b><i class="fa fa-user-plus"></i> Add Employee</b></h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 25px; max-height: 70vh; overflow-y: auto;">
                <form method="POST" action="{{ route('employees.store') }}" id="add-employee-form">
                    @csrf
                    
                    <!-- Section 1: Informasi Dasar -->
                    <div class="card mb-3" style="border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fa fa-info-circle text-primary"></i> Informasi Dasar</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="font-weight-bold">Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" placeholder="Enter Employee Name" id="name" name="name" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="employee_code" class="font-weight-bold">Employee Code <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" placeholder="Enter Employee Code" id="employee_code" name="employee_code" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nik" class="font-weight-bold">NIK</label>
                                        <input type="text" class="form-control" placeholder="Nomor Induk Kependudukan" id="nik" name="nik" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tanggal_lahir" class="font-weight-bold">Tanggal Lahir</label>
                                        <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Pendidikan & Kontrak -->
                    <div class="card mb-3" style="border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fa fa-graduation-cap text-success"></i> Pendidikan & Kontrak</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pendidikan" class="font-weight-bold">Pendidikan</label>
                                        <select class="form-control" id="pendidikan" name="pendidikan">
                                            <option value="">Select Pendidikan</option>
                                            <option value="SD">SD</option>
                                            <option value="SMP">SMP</option>
                                            <option value="SMA/SMK">SMA/SMK</option>
                                            <option value="D1">D1</option>
                                            <option value="D2">D2</option>
                                            <option value="D3">D3</option>
                                            <option value="D4">D4</option>
                                            <option value="S1">S1</option>
                                            <option value="S2">S2</option>
                                            <option value="S3">S3</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kontrak_kerja" class="font-weight-bold">Kontrak Kerja</label>
                                        <select class="form-control" id="kontrak_kerja" name="kontrak_kerja" onchange="toggleDurasiKontrak()">
                                            <option value="">Select Kontrak Kerja</option>
                                            <option value="Tetap">Tetap</option>
                                            <option value="Kontrak">Kontrak</option>
                                            <option value="Magang">Magang</option>
                                            <option value="PKL">PKL</option>
                                            <option value="Freelance">Freelance</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="durasi_kontrak_group" style="display: none;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kontrak_durasi" class="font-weight-bold">Durasi Kontrak (Bulan) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="kontrak_durasi" name="kontrak_durasi" 
                                            placeholder="Masukkan durasi dalam bulan" min="1" max="120" />
                                        <small class="form-text text-muted">Contoh: 3 (3 bulan), 6 (6 bulan), 12 (1 tahun)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hire_date" class="font-weight-bold">Tanggal Mulai Kontrak <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="hire_date" name="hire_date" required />
                                        <small class="form-text text-muted">Tanggal mulai kerja karyawan</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="hire_date_group">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hire_date_tetap" class="font-weight-bold">Tanggal Mulai Kontrak <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="hire_date_tetap" name="hire_date" required />
                                        <small class="form-text text-muted">Tanggal mulai kerja karyawan</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Posisi & Kontak -->
                    <div class="card mb-3" style="border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fa fa-briefcase text-warning"></i> Posisi & Kontak</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="position" class="font-weight-bold">Position <span class="text-danger">*</span></label>
                                        <select class="form-control" id="position" name="position" required>
                                            <option value="">Select Position</option>
                                            <option value="Wadir 1">Wadir 1</option>
                                            <option value="Wadir 2">Wadir 2</option>
                                            <option value="Section Prodi TPMO">Section Prodi TPMO</option>
                                            <option value="Section Prodi TOPKR4">Section Prodi TOPKR4</option>
                                            <option value="Section BAAK">Section BAAK</option>
                                            <option value="Section Teaching Factory">Section Teaching Factory</option>
                                            <option value="Section IT & Sarpras">Section IT & Sarpras</option>
                                            <option value="Section Administrasi">Section Administrasi</option>
                                            <option value="YTI Board of Directors">YTI Board of Directors</option>
                                            <option value="Employees">Employees</option>
                                            <option value="Magang">Magang</option>
                                            <option value="PKL">PKL</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="font-weight-bold">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="email@example.com" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone" class="font-weight-bold">Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone" placeholder="081234567890" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pin_code" class="font-weight-bold">PIN Code</label>
                                        <input type="text" class="form-control" id="pin_code" name="pin_code" placeholder="Enter PIN Code (optional)" />
                                        <small class="form-text text-muted">Minimal 4 karakter</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Status -->
                    <div class="card mb-3" style="border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fa fa-toggle-on text-info"></i> Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status" class="font-weight-bold">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="active" selected>Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary" form="add-employee-form">
                    <i class="fa fa-check"></i> Submit
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleDurasiKontrak() {
    var kontrakKerja = document.getElementById('kontrak_kerja').value;
    var durasiGroup = document.getElementById('durasi_kontrak_group');
    var hireDateGroup = document.getElementById('hire_date_group');
    var durasiInput = document.getElementById('kontrak_durasi');
    var hireDateInput = document.getElementById('hire_date');
    var hireDateTetapInput = document.getElementById('hire_date_tetap');
    
    var perluDurasi = ['Magang', 'Kontrak', 'PKL', 'Freelance'];
    
    if (perluDurasi.includes(kontrakKerja)) {
        durasiGroup.style.display = 'flex';
        hireDateGroup.style.display = 'none';
        durasiInput.setAttribute('required', 'required');
        hireDateInput.setAttribute('required', 'required');
        hireDateTetapInput.removeAttribute('required');
        // Sync value
        if (hireDateTetapInput.value) {
            hireDateInput.value = hireDateTetapInput.value;
        }
    } else {
        durasiGroup.style.display = 'none';
        hireDateGroup.style.display = 'flex';
        durasiInput.removeAttribute('required');
        durasiInput.value = '';
        hireDateInput.removeAttribute('required');
        hireDateTetapInput.setAttribute('required', 'required');
        // Sync value
        if (hireDateInput.value) {
            hireDateTetapInput.value = hireDateInput.value;
        }
    }
}

// Sync hire_date inputs
document.addEventListener('DOMContentLoaded', function() {
    var hireDateInput = document.getElementById('hire_date');
    var hireDateTetapInput = document.getElementById('hire_date_tetap');
    
    if (hireDateInput && hireDateTetapInput) {
        hireDateInput.addEventListener('change', function() {
            hireDateTetapInput.value = this.value;
        });
        
        hireDateTetapInput.addEventListener('change', function() {
            hireDateInput.value = this.value;
        });
    }
});
</script>
