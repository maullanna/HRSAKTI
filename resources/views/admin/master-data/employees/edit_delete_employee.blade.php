<!-- Edit -->
<div class="modal fade" id="edit{{ $employee->id }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>

            </div>
            <h4 class="modal-title"><b><span class="employee_id">Edit Employee</span></b></h4>
            <div class="modal-body text-left">
                <form class="form-horizontal" method="POST" action="{{ route('employees.update', $employee->id) }}">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <div class="form-group">
                        <label for="name" class="col-sm-3 control-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $employee->name }}"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="employee_code" class="col-sm-3 control-label">Employee Code</label>
                        <input type="text" class="form-control" id="employee_code" name="employee_code" value="{{ $employee->employee_code }}"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="nik" class="col-sm-3 control-label">NIK</label>
                        <input type="text" class="form-control" id="nik" name="nik" value="{{ $employee->nik ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label for="tanggal_lahir" class="col-sm-3 control-label">Tanggal Lahir</label>
                        <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="{{ $employee->tanggal_lahir ?? '' }}">
                    </div>
                    <div class="form-group">
                        <label for="pendidikan" class="col-sm-3 control-label">Pendidikan</label>
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
                    <div class="form-group">
                        <label for="kontrak_kerja" class="col-sm-3 control-label">Kontrak Kerja</label>
                        <select class="form-control" id="kontrak_kerja" name="kontrak_kerja">
                            <option value="">Select Kontrak Kerja</option>
                            <option value="Tetap" {{ $employee->kontrak_kerja == 'Tetap' ? 'selected' : '' }}>Tetap</option>
                            <option value="Kontrak" {{ $employee->kontrak_kerja == 'Kontrak' ? 'selected' : '' }}>Kontrak</option>
                            <option value="Magang" {{ $employee->kontrak_kerja == 'Magang' ? 'selected' : '' }}>Magang</option>
                            <option value="PKL" {{ $employee->kontrak_kerja == 'PKL' ? 'selected' : '' }}>PKL</option>
                            <option value="Freelance" {{ $employee->kontrak_kerja == 'Freelance' ? 'selected' : '' }}>Freelance</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="position" class="col-sm-3 control-label">Position</label>
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
                 
                  
                    <div class="form-group">
                        <label for="email" class="col-sm-3 control-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="{{ $employee->email }}" >
                    </div>
                    
                    <div class="form-group">
                        <label for="pin_code" class="col-sm-3 control-label">PIN Code</label>
                        <input type="text" class="form-control" id="pin_code" name="pin_code" 
                            placeholder="Enter PIN Code (optional)">
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i
                        class="fa fa-close"></i> Close</button>
                <button type="submit" class="btn btn-success btn-flat" name="edit"><i class="fa fa-check-square-o"></i>
                    Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete -->
<div class="modal fade" id="delete{{ $employee->id }}">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header " style="align-items: center">
               
              <h4 class="modal-title "><span class="employee_id">Delete Employee</span></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="{{ route('employees.destroy', $employee->id) }}">
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
