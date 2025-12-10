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
                                        <small id="employee_code_help" class="form-text"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nik" class="font-weight-bold">NIK</label>
                                        <input type="text" class="form-control" placeholder="Nomor Induk Kependudukan" id="nik" name="nik" />
                                        <small id="nik_help" class="form-text"></small>
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
                                            <optgroup label="Jabatan Tertinggi">
                                                <option value="Director">Director</option>
                                                <option value="YTI Board of Directors">YTI Board of Directors</option>
                                            </optgroup>
                                            <optgroup label="Level 2 - Wakil Direktur">
                                                <option value="Wadir 1">Wadir 1</option>
                                                <option value="Wadir 2">Wadir 2</option>
                                            </optgroup>
                                            <optgroup label="Level 3 - Section & SDM">
                                                <option value="Section IT & Sarpras">Section IT & Sarpras</option>
                                                <option value="Section Prodi TPMO">Section Prodi TPMO</option>
                                                <option value="Section Prodi TOPKR4">Section Prodi TOPKR4</option>
                                                <option value="Section BAAK">Section BAAK</option>
                                                <option value="Section Teaching Factory">Section Teaching Factory</option>
                                                <option value="Section Administrasi">Section Administrasi</option>
                                                <option value="SDM/HRD">SDM/HRD</option>
                                            </optgroup>
                                            <optgroup label="Level 4 - Karyawan">
                                                <option value="Employees">Employees</option>
                                                <option value="Magang">Magang</option>
                                                <option value="PKL">PKL</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="font-weight-bold">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="email@example.com" />
                                        <small id="email_help" class="form-text"></small>
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

                    <!-- Section 4: Organisasi -->
                    <div class="card mb-3" style="border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fa fa-sitemap text-primary"></i> Organisasi <span class="text-danger">*</span></h5>
                            <small class="text-muted">Field ini wajib diisi untuk dapat melakukan request overtime</small>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4" id="section-field">
                                    <div class="form-group">
                                        <label for="id_section" class="font-weight-bold">Section <span class="text-danger">*</span></label>
                                        <select class="form-control" id="id_section" name="id_section" required>
                                            <option value="">Select Section</option>
                                            @foreach(\App\Models\Section::where('is_active', true)->get() as $section)
                                            <option value="{{ $section->id_section }}" data-name="{{ $section->name }}" {{ old('id_section') == $section->id_section ? 'selected' : '' }}>
                                                {{ $section->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <small id="section-help" class="form-text text-danger">Wajib diisi</small>
                                    </div>
                                </div>
                                <div class="col-md-4" id="wadir-field">
                                    <div class="form-group">
                                        <label for="id_wadir_employee" class="font-weight-bold">Wadir</label>
                                        <select class="form-control" id="id_wadir_employee" name="id_wadir_employee">
                                            <option value="">Select Wadir</option>
                                            @foreach(\App\Models\Employee::whereIn('position', ['Wadir 1', 'Wadir 2'])->where('status', 'active')->get() as $wadir)
                                            <option value="{{ $wadir->id_employees }}" {{ old('id_wadir_employee') == $wadir->id_employees ? 'selected' : '' }}>
                                                {{ $wadir->name }} ({{ $wadir->position }})
                                            </option>
                                            @endforeach
                                        </select>
                                        <small id="wadir-help" class="form-text text-muted"></small>
                                    </div>
                                </div>
                                <div class="col-md-4" id="sdm-field">
                                    <div class="form-group">
                                        <label for="id_sdm_employee" class="font-weight-bold">SDM/HRD <span class="text-danger">*</span></label>
                                        <select class="form-control" id="id_sdm_employee" name="id_sdm_employee">
                                            <option value="">Select SDM/HRD</option>
                                            @foreach(\App\Models\Employee::where('position', 'SDM/HRD')->where('status', 'active')->get() as $sdm)
                                            <option value="{{ $sdm->id_employees }}" {{ old('id_sdm_employee') == $sdm->id_employees ? 'selected' : '' }}>
                                                {{ $sdm->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <small id="sdm-help" class="form-text text-danger">Wajib diisi</small>
                                    </div>
                                </div>
                                <div class="col-md-4" id="director-field">
                                    <div class="form-group">
                                        <label for="id_director_employee" class="font-weight-bold">Director</label>
                                        <select class="form-control" id="id_director_employee" name="id_director_employee">
                                            <option value="">Select Director</option>
                                            @foreach(\App\Models\Employee::whereIn('position', ['Director', 'YTI Board of Directors'])->where('status', 'active')->get() as $director)
                                            <option value="{{ $director->id_employees }}" {{ old('id_director_employee') == $director->id_employees ? 'selected' : '' }}>
                                                {{ $director->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <small id="director-help" class="form-text text-muted"></small>
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

    // Position-based form logic
    function handlePositionChange() {
        var positionSelect = document.getElementById('position');
        if (!positionSelect) {
            console.error('Position select not found');
            return;
        }

        var position = positionSelect.value;
        if (!position) {
            // Reset semua field jika position kosong
            var sectionField = document.getElementById('id_section');
            var sectionHelp = document.getElementById('section-help');
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

        var sectionFieldContainer = document.getElementById('section-field');
        var sectionField = document.getElementById('id_section');
        var sectionHelp = document.getElementById('section-help');
        var wadirField = document.getElementById('wadir-field');
        var wadirSelect = document.getElementById('id_wadir_employee');
        var sdmField = document.getElementById('sdm-field');
        var sdmSelect = document.getElementById('id_sdm_employee');
        var directorField = document.getElementById('director-field');
        var directorSelect = document.getElementById('id_director_employee');

        if (!sectionField || !sectionHelp) {
            console.error('Section field or help not found');
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

            console.log('Mencari section untuk position:', sectionName);
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


    // Real-time validation for unique fields
    function setupUniqueValidation(fieldName, inputId, helpId) {
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
                            value: value
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
                            value: value
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

    // Sync hire_date inputs
    document.addEventListener('DOMContentLoaded', function() {
        // Setup real-time validation for unique fields
        setupUniqueValidation('employee_code', 'employee_code', 'employee_code_help');
        setupUniqueValidation('email', 'email', 'email_help');
        setupUniqueValidation('nik', 'nik', 'nik_help');
        // Trigger position change handler on page load
        var positionSelect = document.getElementById('position');
        if (positionSelect) {
            positionSelect.addEventListener('change', handlePositionChange);
            // Trigger on page load if position already selected
            if (positionSelect.value) {
                handlePositionChange();
            }
        }

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