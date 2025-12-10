<!-- Edit -->
@php
$empId = $employee->id_employees;
@endphp
<div class="modal fade" id="edit{{ $empId }}">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title"><b><i class="fa fa-user-edit"></i> Edit Employee</b></h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 25px; max-height: 70vh; overflow-y: auto;">
                <form class="form-horizontal" method="POST" action="{{ route('employees.update', $empId) }}" id="edit-form-{{ $empId }}">
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
                                        <label for="name{{ $empId }}" class="font-weight-bold">Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name{{ $empId }}" name="name" value="{{ $employee->name }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="employee_code{{ $empId }}" class="font-weight-bold">Employee Code <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="employee_code{{ $empId }}" name="employee_code" value="{{ $employee->employee_code }}" required>
                                        <small id="employee_code_help{{ $empId }}" class="form-text"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nik{{ $empId }}" class="font-weight-bold">NIK</label>
                                        <input type="text" class="form-control" id="nik{{ $empId }}" name="nik" value="{{ $employee->nik ?? '' }}" placeholder="Nomor Induk Kependudukan">
                                        <small id="nik_help{{ $empId }}" class="form-text"></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tanggal_lahir{{ $empId }}" class="font-weight-bold">Tanggal Lahir</label>
                                        <input type="date" class="form-control" id="tanggal_lahir{{ $empId }}" name="tanggal_lahir" value="{{ $employee->tanggal_lahir ?? '' }}">
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
                                        <label for="pendidikan{{ $empId }}" class="font-weight-bold">Pendidikan</label>
                                        <select class="form-control" id="pendidikan{{ $empId }}" name="pendidikan">
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
                                        <select class="form-control kontrak-kerja-select" id="kontrak_kerja{{ $empId }}"
                                            name="kontrak_kerja" data-emp-id="{{ $empId }}">
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
                            <div class="row" id="durasi_kontrak_group{{ $empId }}"
                                @if(in_array($employee->kontrak_kerja, ['Magang', 'Kontrak', 'PKL', 'Freelance']))
                                style="display: flex;"
                                @else
                                style="display: none;"
                                @endif>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kontrak_durasi{{ $empId }}" class="font-weight-bold">Durasi Kontrak (Bulan) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="kontrak_durasi{{ $empId }}"
                                            name="kontrak_durasi" value="{{ $employee->kontrak_durasi ?? '' }}"
                                            placeholder="Masukkan durasi dalam bulan" min="1" max="120" />
                                        <small class="form-text text-muted">Contoh: 3 (3 bulan), 6 (6 bulan), 12 (1 tahun)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hire_date{{ $empId }}" class="font-weight-bold">Tanggal Mulai Kontrak <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="hire_date{{ $empId }}"
                                            name="hire_date" value="{{ $employee->hire_date ?? '' }}" />
                                        <small class="form-text text-muted">Tanggal mulai kerja karyawan</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="hire_date_group{{ $empId }}"
                                @if(!in_array($employee->kontrak_kerja, ['Magang', 'Kontrak', 'PKL', 'Freelance']))
                                style="display: flex;"
                                @else
                                style="display: none;"
                                @endif>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="hire_date_tetap{{ $empId }}" class="font-weight-bold">Tanggal Mulai Kontrak <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="hire_date_tetap{{ $empId }}"
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
                                        <label for="position{{ $empId }}" class="font-weight-bold">Position <span class="text-danger">*</span></label>
                                        <select class="form-control" id="position{{ $empId }}" name="position" required>
                                            <option value="">Select Position</option>
                                            <optgroup label="Jabatan Tertinggi">
                                                <option value="Director" {{ $employee->position == 'Director' ? 'selected' : '' }}>Director</option>
                                                <option value="YTI Board of Directors" {{ $employee->position == 'YTI Board of Directors' ? 'selected' : '' }}>YTI Board of Directors</option>
                                            </optgroup>
                                            <optgroup label="Level 2 - Wakil Direktur">
                                                <option value="Wadir 1" {{ $employee->position == 'Wadir 1' ? 'selected' : '' }}>Wadir 1</option>
                                                <option value="Wadir 2" {{ $employee->position == 'Wadir 2' ? 'selected' : '' }}>Wadir 2</option>
                                            </optgroup>
                                            <optgroup label="Level 3 - Section & SDM">
                                                <option value="Section IT & Sarpras" {{ $employee->position == 'Section IT & Sarpras' ? 'selected' : '' }}>Section IT & Sarpras</option>
                                                <option value="Section Prodi TPMO" {{ $employee->position == 'Section Prodi TPMO' ? 'selected' : '' }}>Section Prodi TPMO</option>
                                                <option value="Section Prodi TOPKR4" {{ $employee->position == 'Section Prodi TOPKR4' ? 'selected' : '' }}>Section Prodi TOPKR4</option>
                                                <option value="Section BAAK" {{ $employee->position == 'Section BAAK' ? 'selected' : '' }}>Section BAAK</option>
                                                <option value="Section Teaching Factory" {{ $employee->position == 'Section Teaching Factory' ? 'selected' : '' }}>Section Teaching Factory</option>
                                                <option value="Section Administrasi" {{ $employee->position == 'Section Administrasi' ? 'selected' : '' }}>Section Administrasi</option>
                                                <option value="SDM/HRD" {{ $employee->position == 'SDM/HRD' ? 'selected' : '' }}>SDM/HRD</option>
                                            </optgroup>
                                            <optgroup label="Level 4 - Karyawan">
                                                <option value="Employees" {{ $employee->position == 'Employees' ? 'selected' : '' }}>Employees</option>
                                                <option value="Magang" {{ $employee->position == 'Magang' ? 'selected' : '' }}>Magang</option>
                                                <option value="PKL" {{ $employee->position == 'PKL' ? 'selected' : '' }}>PKL</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email{{ $empId }}" class="font-weight-bold">Email</label>
                                        <input type="email" class="form-control" id="email{{ $empId }}" name="email"
                                            value="{{ $employee->email }}" placeholder="email@example.com">
                                        <small id="email_help{{ $empId }}" class="form-text"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone{{ $empId }}" class="font-weight-bold">Phone</label>
                                        <input type="text" class="form-control" id="phone{{ $empId }}" name="phone"
                                            value="{{ $employee->phone ?? '' }}" placeholder="081234567890">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pin_code{{ $empId }}" class="font-weight-bold">PIN Code</label>
                                        <input type="text" class="form-control" id="pin_code{{ $empId }}" name="pin_code"
                                            placeholder="Enter PIN Code (optional, leave blank to keep current)">
                                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah PIN</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Organisasi -->
                    <div class="card mb-3" style="border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fa fa-sitemap text-primary"></i> Organisasi <span class="text-danger">*</span></h5>
                            <small class="text-muted">Field ini wajib diisi untuk dapat melakukan request overtime</small>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4" id="section-field{{ $empId }}">
                                    <div class="form-group">
                                        <label for="id_section{{ $empId }}" class="font-weight-bold">Section <span class="text-danger">*</span></label>
                                        <select class="form-control" id="id_section{{ $empId }}" name="id_section" required>
                                            <option value="">Select Section</option>
                                            @foreach(\App\Models\Section::where('is_active', true)->get() as $section)
                                            <option value="{{ $section->id_section }}" data-name="{{ $section->name }}" {{ (old('id_section', $employee->id_section) == $section->id_section) ? 'selected' : '' }}>
                                                {{ $section->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <small id="section-help{{ $empId }}" class="form-text text-danger">Wajib diisi</small>
                                    </div>
                                </div>
                                <div class="col-md-4" id="wadir-field{{ $empId }}">
                                    <div class="form-group">
                                        <label for="id_wadir_employee{{ $empId }}" class="font-weight-bold">Wadir</label>
                                        <select class="form-control" id="id_wadir_employee{{ $empId }}" name="id_wadir_employee">
                                            <option value="">Select Wadir</option>
                                            @foreach(\App\Models\Employee::whereIn('position', ['Wadir 1', 'Wadir 2'])->where('status', 'active')->get() as $wadir)
                                            <option value="{{ $wadir->id_employees }}" {{ (old('id_wadir_employee', $employee->id_wadir_employee) == $wadir->id_employees) ? 'selected' : '' }}>
                                                {{ $wadir->name }} ({{ $wadir->position }})
                                            </option>
                                            @endforeach
                                        </select>
                                        <small id="wadir-help{{ $empId }}" class="form-text text-muted"></small>
                                    </div>
                                </div>
                                <div class="col-md-4" id="sdm-field{{ $empId }}">
                                    <div class="form-group">
                                        <label for="id_sdm_employee{{ $empId }}" class="font-weight-bold">SDM/HRD <span class="text-danger">*</span></label>
                                        <select class="form-control" id="id_sdm_employee{{ $empId }}" name="id_sdm_employee">
                                            <option value="">Select SDM/HRD</option>
                                            @foreach(\App\Models\Employee::where('position', 'SDM/HRD')->where('status', 'active')->get() as $sdm)
                                            <option value="{{ $sdm->id_employees }}" {{ (old('id_sdm_employee', $employee->id_sdm_employee) == $sdm->id_employees) ? 'selected' : '' }}>
                                                {{ $sdm->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <small id="sdm-help{{ $empId }}" class="form-text text-danger">Wajib diisi</small>
                                    </div>
                                </div>
                                <div class="col-md-4" id="director-field{{ $empId }}">
                                    <div class="form-group">
                                        <label for="id_director_employee{{ $empId }}" class="font-weight-bold">Director</label>
                                        <select class="form-control" id="id_director_employee{{ $empId }}" name="id_director_employee">
                                            <option value="">Select Director</option>
                                            @foreach(\App\Models\Employee::whereIn('position', ['Director', 'YTI Board of Directors'])->where('status', 'active')->get() as $director)
                                            <option value="{{ $director->id_employees }}" {{ (old('id_director_employee', $employee->id_director_employee) == $director->id_employees) ? 'selected' : '' }}>
                                                {{ $director->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <small id="director-help{{ $empId }}" class="form-text text-muted"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 5: Status -->
                    <div class="card mb-3" style="border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fa fa-toggle-on text-info"></i> Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status{{ $empId }}" class="font-weight-bold">Status</label>
                                        <select class="form-control" id="status{{ $empId }}" name="status">
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
                <button type="submit" class="btn btn-success" form="edit-form-{{ $empId }}">
                    <i class="fa fa-check"></i> Update Employee
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleDurasiKontrakEdit(empId) {
        var kontrakKerja = document.getElementById('kontrak_kerja' + empId).value;
        var durasiGroup = document.getElementById('durasi_kontrak_group' + empId);
        var hireDateGroup = document.getElementById('hire_date_group' + empId);
        var durasiInput = document.getElementById('kontrak_durasi' + empId);
        var hireDateInput = document.getElementById('hire_date' + empId);
        var hireDateTetapInput = document.getElementById('hire_date_tetap' + empId);

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
            durasiInput.value = '';
            hireDateInput.removeAttribute('required');
            hireDateTetapInput.setAttribute('required', 'required');
        }
    }

    // Position-based form logic for edit form
    function handlePositionChangeEdit(empId) {
        var positionSelect = document.getElementById('position' + empId);
        if (!positionSelect) return;

        var position = positionSelect.value;
        if (!position) {
            // Reset semua field jika position kosong
            var sectionField = document.getElementById('id_section' + empId);
            var sectionHelp = document.getElementById('section-help' + empId);
            if (sectionField) {
                sectionField.readOnly = false;
                sectionField.style.backgroundColor = '';
                sectionField.style.cursor = '';
                sectionField.value = '';
            }
            if (sectionHelp) {
                sectionHelp.textContent = 'Wajib diisi';
                sectionHelp.className = 'form-text text-danger';
            }
            return;
        }

        var isSection = position && position.startsWith('Section ');
        var isKaryawan = ['Employees', 'Magang', 'PKL'].includes(position);
        var isWadir = ['Wadir 1', 'Wadir 2'].includes(position);
        var isDirector = position === 'Director' || position === 'YTI Board of Directors';
        var isSdm = position === 'SDM/HRD';

        var sectionFieldContainer = document.getElementById('section-field' + empId);
        var sectionField = document.getElementById('id_section' + empId);
        var sectionHelp = document.getElementById('section-help' + empId);
        var wadirField = document.getElementById('wadir-field' + empId);
        var wadirSelect = document.getElementById('id_wadir_employee' + empId);
        var sdmField = document.getElementById('sdm-field' + empId);
        var sdmSelect = document.getElementById('id_sdm_employee' + empId);
        var directorField = document.getElementById('director-field' + empId);
        var directorSelect = document.getElementById('id_director_employee' + empId);

        if (!sectionField || !sectionHelp) {
            console.error('Section field or help not found for empId:', empId);
            return;
        }

        // Section Field Logic
        if (isSection) {
            // Jika Section, tampilkan dan auto-set section dari position
            if (sectionFieldContainer) {
                sectionFieldContainer.style.display = 'block';
            }
            var sectionName = position.trim(); // "Section IT & Sarpras"
            var sectionOptions = sectionField.querySelectorAll('option[data-name]');
            var found = false;

            console.log('Mencari section untuk position:', sectionName, '(empId:', empId + ')');
            console.log('Jumlah section options:', sectionOptions.length);

            // Use for loop instead of forEach to allow break
            for (var i = 0; i < sectionOptions.length; i++) {
                var option = sectionOptions[i];
                var optionName = option.getAttribute('data-name');
                if (optionName) {
                    optionName = optionName.trim();
                    console.log('Membandingkan:', optionName, 'dengan', sectionName);
                    // Match exact (case-sensitive untuk akurasi)
                    if (optionName === sectionName) {
                        sectionField.value = option.value;
                        sectionField.readOnly = true;
                        sectionField.style.backgroundColor = '#e9ecef';
                        sectionField.style.cursor = 'not-allowed';
                        sectionField.setAttribute('required', 'required');
                        if (sectionHelp) {
                            sectionHelp.textContent = '✓ Auto-set dari position: ' + sectionName;
                            sectionHelp.className = 'form-text text-success';
                        }
                        found = true;
                        console.log('Section ditemukan dan di-set ke:', option.value);
                        break; // Break loop
                    }
                }
            }

            if (!found) {
                console.warn('Section tidak ditemukan untuk position:', sectionName);
                if (sectionHelp) {
                    sectionHelp.textContent = '⚠ Section "' + sectionName + '" tidak ditemukan. Pastikan section sudah dibuat di master data Section.';
                    sectionHelp.className = 'form-text text-danger';
                }
                sectionField.readOnly = false;
                sectionField.style.backgroundColor = '';
                sectionField.style.cursor = '';
                sectionField.value = '';
                sectionField.setAttribute('required', 'required');
            }
        } else if (isKaryawan) {
            // Jika Karyawan, tampilkan dan wajib pilih section
            if (sectionFieldContainer) {
                sectionFieldContainer.style.display = 'block';
            }
            sectionField.readOnly = false;
            sectionField.style.backgroundColor = '';
            sectionField.style.cursor = '';
            sectionField.value = '';
            sectionField.setAttribute('required', 'required');
            if (sectionHelp) {
                sectionHelp.textContent = 'Wajib pilih section';
                sectionHelp.className = 'form-text text-danger';
            }
        } else if (isDirector) {
            // Jika Director (jabatan tertinggi), sembunyikan semua field organisasi
            if (sectionFieldContainer) {
                sectionFieldContainer.style.display = 'none';
            }
            sectionField.removeAttribute('required');
            sectionField.value = '';
        } else if (isWadir) {
            // Jika Wadir, sembunyikan semua field organisasi (otomatis anak buah Director)
            if (sectionFieldContainer) {
                sectionFieldContainer.style.display = 'none';
            }
            sectionField.removeAttribute('required');
            sectionField.value = '';
        } else {
            // Jika Section/SDM/Karyawan, tampilkan dan wajib pilih section
            if (sectionFieldContainer) {
                sectionFieldContainer.style.display = 'block';
            }
            sectionField.readOnly = false;
            sectionField.style.backgroundColor = '';
            sectionField.style.cursor = '';
            sectionField.value = '';
            if (isSection || isKaryawan) {
                sectionField.setAttribute('required', 'required');
                if (sectionHelp) {
                    sectionHelp.textContent = 'Wajib pilih section';
                    sectionHelp.className = 'form-text text-danger';
                }
            } else {
                sectionField.removeAttribute('required');
                if (sectionHelp) {
                    sectionHelp.textContent = 'Opsional';
                    sectionHelp.className = 'form-text text-muted';
                }
            }
        }

        // Wadir Field Logic
        if (isWadir || isDirector) {
            // Wadir dan Director tidak perlu input Wadir
            wadirField.style.display = 'none';
            wadirSelect.removeAttribute('required');
            wadirSelect.value = '';
        } else {
            wadirField.style.display = 'block';
            if (isSection || isKaryawan) {
                wadirSelect.setAttribute('required', 'required');
            } else {
                wadirSelect.removeAttribute('required');
            }
        }

        // SDM Field Logic
        if (isSdm) {
            sdmField.style.display = 'none';
            sdmSelect.removeAttribute('required');
            sdmSelect.value = '';
        } else if (isWadir || isDirector) {
            // Wadir dan Director tidak perlu input SDM
            sdmField.style.display = 'none';
            sdmSelect.removeAttribute('required');
            sdmSelect.value = '';
        } else {
            sdmField.style.display = 'block';
            if (isSection || isKaryawan) {
                sdmSelect.setAttribute('required', 'required');
            } else {
                sdmSelect.removeAttribute('required');
            }
        }

        // Director Field Logic
        // Semua position tidak perlu input director karena otomatis terhubung ke Director
        directorField.style.display = 'none';
        directorSelect.removeAttribute('required');
        directorSelect.value = '';
    }

    // Real-time validation for unique fields (edit form)
    function setupUniqueValidationEdit(fieldName, inputId, helpId, empId) {
        var input = document.getElementById(inputId);
        var helpElement = document.getElementById(helpId);
        var timeout;

        if (!input) return;

        input.addEventListener('input', function() {
            var value = this.value.trim();

            // Clear previous timeout
            clearTimeout(timeout);

            // Remove previous validation classes
            if (helpElement) {
                helpElement.textContent = '';
                helpElement.className = 'form-text';
            }
            input.classList.remove('is-invalid', 'is-valid');

            // Skip validation if empty (for optional fields)
            if (!value && (fieldName === 'email' || fieldName === 'nik')) {
                return;
            }

            // Required field validation
            if (!value && fieldName === 'employee_code') {
                if (helpElement) {
                    helpElement.textContent = 'Employee code is required.';
                    helpElement.className = 'form-text text-danger';
                }
                input.classList.add('is-invalid');
                return;
            }

            // Debounce: wait 500ms after user stops typing
            timeout = setTimeout(function() {
                // Make AJAX request to check uniqueness
                fetch('{{ route("employees.check-unique") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            field: fieldName,
                            value: value,
                            exclude_id: empId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            input.classList.remove('is-valid');
                            input.classList.add('is-invalid');
                            if (helpElement) {
                                helpElement.textContent = data.message || 'This ' + fieldName + ' is already registered.';
                                helpElement.className = 'form-text text-danger';
                            }
                        } else {
                            input.classList.remove('is-invalid');
                            input.classList.add('is-valid');
                            if (helpElement) {
                                helpElement.textContent = data.message || 'This ' + fieldName + ' is available.';
                                helpElement.className = 'form-text text-success';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Validation error:', error);
                    });
            }, 500);
        });

        // Also validate on blur
        input.addEventListener('blur', function() {
            clearTimeout(timeout);
            var value = this.value.trim();

            if (!value && (fieldName === 'email' || fieldName === 'nik')) {
                return;
            }

            if (!value && fieldName === 'employee_code') {
                if (helpElement) {
                    helpElement.textContent = 'Employee code is required.';
                    helpElement.className = 'form-text text-danger';
                }
                input.classList.add('is-invalid');
                return;
            }

            if (value) {
                fetch('{{ route("employees.check-unique") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            field: fieldName,
                            value: value,
                            exclude_id: empId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            input.classList.remove('is-valid');
                            input.classList.add('is-invalid');
                            if (helpElement) {
                                helpElement.textContent = data.message || 'This ' + fieldName + ' is already registered.';
                                helpElement.className = 'form-text text-danger';
                            }
                        } else {
                            input.classList.remove('is-invalid');
                            input.classList.add('is-valid');
                            if (helpElement) {
                                helpElement.textContent = data.message || 'This ' + fieldName + ' is available.';
                                helpElement.className = 'form-text text-success';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Validation error:', error);
                    });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        var empId = '{{ $empId }}';

        // Setup real-time validation for unique fields
        setupUniqueValidationEdit('employee_code', 'employee_code' + empId, 'employee_code_help' + empId, empId);
        setupUniqueValidationEdit('email', 'email' + empId, 'email_help' + empId, empId);
        setupUniqueValidationEdit('nik', 'nik' + empId, 'nik_help' + empId, empId);

        // Setup position change handler
        var positionSelect = document.getElementById('position' + empId);
        if (positionSelect) {
            positionSelect.addEventListener('change', function() {
                handlePositionChangeEdit(empId);
            });
            // Trigger on page load if position already selected
            if (positionSelect.value) {
                handlePositionChangeEdit(empId);
            }
        }


        var kontrakSelect = document.getElementById('kontrak_kerja' + empId);
        if (kontrakSelect) {
            kontrakSelect.addEventListener('change', function() {
                toggleDurasiKontrakEdit(empId);
            });
        }

        var hireDateInput = document.getElementById('hire_date' + empId);
        var hireDateTetapInput = document.getElementById('hire_date_tetap' + empId);

        if (hireDateInput && hireDateTetapInput) {
            hireDateInput.addEventListener('change', function() {
                hireDateTetapInput.value = this.value;
            });

            hireDateTetapInput.addEventListener('change', function() {
                hireDateInput.value = this.value;
            });

            // Sinkronisasi awal
            if (hireDateInput.value) {
                hireDateTetapInput.value = hireDateInput.value;
            } else if (hireDateTetapInput.value) {
                hireDateInput.value = hireDateTetapInput.value;
            }
        }
    });
</script>


<!-- Delete -->
<div class="modal fade" id="delete{{ $empId }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header " style="align-items: center">

                <h4 class="modal-title "><span class="employee_id">Delete Employee</span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="{{ route('employees.destroy', $empId) }}">
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