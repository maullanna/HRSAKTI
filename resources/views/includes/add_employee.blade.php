<!-- Add -->
<div class="modal fade" id="addnew">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>

            </div>

            <h4 class="modal-title"><b>Add Employee</b></h4>
            <div class="modal-body">

                <div class="card-body text-left">

                    <form method="POST" action="{{ route('employees.store') }}">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" placeholder="Enter Employee Name" id="name" name="name"
                                required />
                        </div>
                        <div class="form-group">
                            <label for="employee_code">Employee Code</label>
                            <input type="text" class="form-control" placeholder="Enter Employee Code" id="employee_code" name="employee_code"
                                required />
                        </div>
                        <div class="form-group">
                            <label for="nik">NIK</label>
                            <input type="text" class="form-control" placeholder="Enter NIK" id="nik" name="nik" />
                        </div>
                        <div class="form-group">
                            <label for="tanggal_lahir">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" />
                        </div>
                        <div class="form-group">
                            <label for="pendidikan">Pendidikan</label>
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
                        <div class="form-group">
                            <label for="kontrak_kerja">Kontrak Kerja</label>
                            <select class="form-control" id="kontrak_kerja" name="kontrak_kerja">
                                <option value="">Select Kontrak Kerja</option>
                                <option value="Tetap">Tetap</option>
                                <option value="Kontrak">Kontrak</option>
                                <option value="Magang">Magang</option>
                                <option value="PKL">PKL</option>
                                <option value="Freelance">Freelance</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="position">Position</label>
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

                        
                        <div class="form-group">
                            <label for="email" class="col-sm-3 control-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        
                        <div class="form-group">
                            <label for="pin_code" class="col-sm-3 control-label">PIN Code</label>
                            <input type="text" class="form-control" id="pin_code" name="pin_code" placeholder="Enter PIN Code (optional)">
                        </div>

                        <div class="form-group">
                            <div>
                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    Submit
                                </button>
                                <button type="reset" class="btn btn-secondary waves-effect m-l-5" data-dismiss="modal">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>


        </div>

    </div>
</div>
</div>