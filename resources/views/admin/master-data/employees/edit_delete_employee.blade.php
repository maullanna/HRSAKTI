<!-- Edit -->
<div class="modal fade" id="edit{{ $employee->id_employees }}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title"><b><i class="fa fa-user-edit"></i> Edit Employee</b></h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 25px; max-height: 70vh; overflow-y: auto;">
                <form class="form-horizontal" method="POST" action="{{ route('employees.update', $employee->id_employees) }}" id="edit-form-{{ $employee->id_employees }}">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    
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
                                        <input type="text" class="form-control" id="name" name="name" value="{{ $employee->name }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="employee_code" class="font-weight-bold">Employee Code <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="employee_code" name="employee_code" value="{{ $employee->employee_code }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nik" class="font-weight-bold">NIK</label>
                                        <input type="text" class="form-control" id="nik" name="nik" value="{{ $employee->nik ?? '' }}" placeholder="Nomor Induk Kependudukan">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tanggal_lahir" class="font-weight-bold">Tanggal Lahir</label>
                                        <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="{{ $employee->tanggal_lahir ?? '' }}">
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
                                            <option value="SD" {{ $employee->pendidikan == 'SD' ? 'selected' : '' }}>SD</option>
                                            <option value="SMP" {{ $employee->pendidikan == 'SMP' ? 'selected' : '' }}>SMP</option>
                                            <option value="SMA/SMK" {{ $employee->pendidikan == 'SMA/SMK' ? 'selected' : '' }}>SMA/SMK</option>
                                            <option value="D1" {{ $employee->pendidikan == 'D1' ? 'selected' : '' }}>D1</option>
                                            <option value="D2" {{ $employee->pendidikan == 'D2' ? 'selected' : '' }}>D2</option>
                                            <option value="D3" {{ $employee->pendidikan == 'D3' ? 'selected' : '' }}>D3</option>
                                            <option value="D4" {{ $employee->pendidikan == 'D4' ? 'selected' : '' }}>D4</option>
                                            <option value="S1" {{ $employee->pendidikan == 'S1' ? 'selected' : '' }}>S1</option>
                                            <option value="S2" {{ $employee->pendidikan == 'S2' ? 'selected' : '' }}>S2</option>
                                            <option value="S3" {{ $employee->pendidikan == 'S3' ? 'selected' : '' }}>S3</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kontrak_kerja" class="font-weight-bold">Kontrak Kerja</label>
                                        <select class="form-control" id="kontrak_kerja{{ $employee->id_employees }}" 
                                            name="kontrak_kerja" onchange="toggleDurasiKontrakEdit{{ $employee->id_employees }}()">
                                            <option value="">Select Kontrak Kerja</option>
                                            <option value="Tetap" {{ $employee->kontrak_kerja == 'Tetap' ? 'selected' : '' }}>Tetap</option>
                                            <option value="Kontrak" {{ $employee->kontrak_kerja == 'Kontrak' ? 'selected' : '' }}>Kontrak</option>
                                            <option value="Magang" {{ $employee->kontrak_kerja == 'Magang' ? 'selected' : '' }}>Magang</option>
                                            <option value="PKL" {{ $employee->kontrak_kerja == 'PKL' ? 'selected' : '' }}>PKL</option>
                                            <option value="Freelance" {{ $employee->kontrak_kerja == 'Freelance' ? 'selected' : '' }}>Freelance</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="durasi_kontrak_group{{ $employee->id_employees }}" 
                                style="display: {{ in_array($employee->kontrak_kerja, ['Magang', 'Kontrak', 'PKL', 'Freelance']) ? 'flex' : 'none' }};">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kontrak_durasi{{ $employee->id_employees }}" class="font-weight-bold">Durasi Kontrak (Bulan) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="kontrak_durasi{{ $employee->id_employees }}" 
                                            name="kontrak_durasi" value="{{ $employee->kontrak_durasi ?? '' }}" 
                                            placeholder="Masukkan durasi dalam bulan" min="1" max="120" />
                                        <small class="form-text text-muted">Contoh: 3 (3 bulan), 6 (6 bulan), 12 (1 tahun)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hire_date{{ $employee->id_employees }}" class="font-weight-bold">Tanggal Mulai Kontrak <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="hire_date{{ $employee->id_employees }}" 
                                            name="hire_date" value="{{ $employee->hire_date ?? '' }}" />
                                        <small class="form-text text-muted">Tanggal mulai kerja karyawan</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="hire_date_group{{ $employee->id_employees }}" 
                                style="display: {{ !in_array($employee->kontrak_kerja, ['Magang', 'Kontrak', 'PKL', 'Freelance']) ? 'flex' : 'none' }};">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hire_date_tetap{{ $employee->id_employees }}" class="font-weight-bold">Tanggal Mulai Kontrak <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="hire_date_tetap{{ $employee->id_employees }}" 
                                            name="hire_date" value="{{ $employee->hire_date ?? '' }}" />
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
                                            <option value="Wadir 1" {{ $employee->position == 'Wadir 1' ? 'selected' : '' }}>Wadir 1</option>
                                            <option value="Wadir 2" {{ $employee->position == 'Wadir 2' ? 'selected' : '' }}>Wadir 2</option>
                                            <option value="Section Prodi TPMO" {{ $employee->position == 'Section Prodi TPMO' ? 'selected' : '' }}>Section Prodi TPMO</option>
                                            <option value="Section Prodi TOPKR4" {{ $employee->position == 'Section Prodi TOPKR4' ? 'selected' : '' }}>Section Prodi TOPKR4</option>
                                            <option value="Section BAAK" {{ $employee->position == 'Section BAAK' ? 'selected' : '' }}>Section BAAK</option>
                                            <option value="Section Teaching Factory" {{ $employee->position == 'Section Teaching Factory' ? 'selected' : '' }}>Section Teaching Factory</option>
                                            <option value="Section IT & Sarpras" {{ $employee->position == 'Section IT & Sarpras' ? 'selected' : '' }}>Section IT & Sarpras</option>
                                            <option value="Section Administrasi" {{ $employee->position == 'Section Administrasi' ? 'selected' : '' }}>Section Administrasi</option>
                                            <option value="YTI Board of Directors" {{ $employee->position == 'YTI Board of Directors' ? 'selected' : '' }}>YTI Board of Directors</option>
                                            <option value="Employees" {{ $employee->position == 'Employees' ? 'selected' : '' }}>Employees</option>
                                            <option value="Magang" {{ $employee->position == 'Magang' ? 'selected' : '' }}>Magang</option>
                                            <option value="PKL" {{ $employee->position == 'PKL' ? 'selected' : '' }}>PKL</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="font-weight-bold">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                            value="{{ $employee->email }}" placeholder="email@example.com">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone" class="font-weight-bold">Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone" 
                                            value="{{ $employee->phone ?? '' }}" placeholder="081234567890">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pin_code" class="font-weight-bold">PIN Code</label>
                                        <input type="text" class="form-control" id="pin_code" name="pin_code" 
                                            placeholder="Enter PIN Code (optional, leave blank to keep current)">
                                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah PIN</small>
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
                                            <option value="active" {{ $employee->status == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ $employee->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                <button type="submit" class="btn btn-success" form="edit-form-{{ $employee->id_employees }}">
                    <i class="fa fa-check"></i> Update Employee
                </button>
            </div>
        </div>
    </div>
</div>

<script>
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
    } else {
        durasiGroup.style.display = 'none';
        hireDateGroup.style.display = 'flex';
        durasiInput.removeAttribute('required');
        if (kontrakKerja !== '{{ $employee->kontrak_kerja }}') {
            durasiInput.value = '';
        }
        hireDateInput.removeAttribute('required');
        hireDateTetapInput.setAttribute('required', 'required');
    }
}

// Sync hire_date inputs for edit form
document.addEventListener('DOMContentLoaded', function() {
    var hireDateInput = document.getElementById('hire_date{{ $employee->id_employees }}');
    var hireDateTetapInput = document.getElementById('hire_date_tetap{{ $employee->id_employees }}');
    
    if (hireDateInput && hireDateTetapInput) {
        hireDateInput.addEventListener('change', function() {
            hireDateTetapInput.value = this.value;
        });
        
        hireDateTetapInput.addEventListener('change', function() {
            hireDateInput.value = this.value;
        });
        
        // Initialize sync
        if (hireDateInput.value) {
            hireDateTetapInput.value = hireDateInput.value;
        } else if (hireDateTetapInput.value) {
            hireDateInput.value = hireDateTetapInput.value;
        }
    }
});
</script>

<!-- Delete -->
<div class="modal fade" id="delete{{ $employee->id_employees }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header " style="align-items: center">
               
              <h4 class="modal-title "><span class="employee_id">Delete Employee</span></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="{{ route('employees.destroy', $employee->id_employees) }}">
                    @csrf
                    {{ method_field('DELETE') }}
                    <div class="text-center">
                        <h6>Are you sure you want to delete:</h6>
                        <h2 class="bold del_employee_name">{{$employee->name}}</h2>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i
                        class="fa fa-close"></i> Close</button>
                <button type="submit" class="btn btn-danger btn-flat"><i class="fa fa-trash"></i> Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
