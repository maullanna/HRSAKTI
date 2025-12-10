@extends('layouts.master')

@section('breadcrumb')
<div class="col-sm-6">
    <h4 class="page-title text-left">Edit Salary</h4>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('salaries.index') }}">Salaries</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
</div>
@endsection

@section('content')
@include('includes.flash')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('salaries.update', $salary) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employee_id">Employee <span class="text-danger">*</span></label>
                                <select name="employee_id" id="employee_id" class="form-control @error('employee_id') is-invalid @enderror" required>
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $employee)
                                    <option value="{{ $employee->id_employees }}" {{ old('employee_id', $salary->employee_id) == $employee->id_employees ? 'selected' : '' }}>
                                        {{ $employee->name }} (ID: {{ $employee->id_employees }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="month">Month <span class="text-danger">*</span></label>
                                <input type="month" name="month" id="month" class="form-control @error('month') is-invalid @enderror"
                                    value="{{ old('month', \Carbon\Carbon::parse($salary->month)->format('Y-m')) }}" required>
                                @error('month')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="basic_salary">Basic Salary <span class="text-danger">*</span></label>
                                <input type="number" name="basic_salary" id="basic_salary" class="form-control @error('basic_salary') is-invalid @enderror"
                                    value="{{ old('basic_salary', $salary->basic_salary) }}" min="0" step="0.01" required>
                                @error('basic_salary')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Allowances</h5>
                            <div id="allowances-container">
                                @if(count($salary->allowances) > 0)
                                @foreach($salary->allowances as $key => $value)
                                <div class="allowance-item mb-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="text" name="allowance_names[]" class="form-control" placeholder="Allowance name" value="{{ $key }}">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" name="allowance_values[]" class="form-control" placeholder="Amount" min="0" step="0.01" value="{{ $value }}">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger btn-sm remove-allowance">Remove</button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @else
                                <div class="allowance-item mb-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="text" name="allowance_names[]" class="form-control" placeholder="Allowance name">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" name="allowance_values[]" class="form-control" placeholder="Amount" min="0" step="0.01">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger btn-sm remove-allowance">Remove</button>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="add-allowance">Add Allowance</button>
                        </div>

                        <div class="col-md-6">
                            <h5>Deductions</h5>
                            <div id="deductions-container">
                                @if(count($salary->deductions) > 0)
                                @foreach($salary->deductions as $key => $value)
                                <div class="deduction-item mb-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="text" name="deduction_names[]" class="form-control" placeholder="Deduction name" value="{{ $key }}">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" name="deduction_values[]" class="form-control" placeholder="Amount" min="0" step="0.01" value="{{ $value }}">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger btn-sm remove-deduction">Remove</button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @else
                                <div class="deduction-item mb-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="text" name="deduction_names[]" class="form-control" placeholder="Deduction name">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" name="deduction_values[]" class="form-control" placeholder="Amount" min="0" step="0.01">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger btn-sm remove-deduction">Remove</button>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="add-deduction">Add Deduction</button>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save mr-2"></i>Update Salary
                        </button>
                        <a href="{{ route('salaries.show', $salary) }}" class="btn btn-info">
                            <i class="mdi mdi-eye mr-2"></i>View Details
                        </a>
                        <a href="{{ route('salaries.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left mr-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add allowance
        document.getElementById('add-allowance').addEventListener('click', function() {
            const container = document.getElementById('allowances-container');
            const newItem = document.querySelector('.allowance-item').cloneNode(true);
            newItem.querySelectorAll('input').forEach(input => input.value = '');
            container.appendChild(newItem);
        });

        // Remove allowance
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-allowance')) {
                if (document.querySelectorAll('.allowance-item').length > 1) {
                    e.target.closest('.allowance-item').remove();
                }
            }
        });

        // Add deduction
        document.getElementById('add-deduction').addEventListener('click', function() {
            const container = document.getElementById('deductions-container');
            const newItem = document.querySelector('.deduction-item').cloneNode(true);
            newItem.querySelectorAll('input').forEach(input => input.value = '');
            container.appendChild(newItem);
        });

        // Remove deduction
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-deduction')) {
                if (document.querySelectorAll('.deduction-item').length > 1) {
                    e.target.closest('.deduction-item').remove();
                }
            }
        });

        // Process form data before submit
        document.querySelector('form').addEventListener('submit', function(e) {
            // Process allowances
            const allowanceNames = document.querySelectorAll('input[name="allowance_names[]"]');
            const allowanceValues = document.querySelectorAll('input[name="allowance_values[]"]');
            const allowances = {};

            allowanceNames.forEach((nameInput, index) => {
                const name = nameInput.value.trim();
                const value = parseFloat(allowanceValues[index].value) || 0;
                if (name && value > 0) {
                    allowances[name] = value;
                }
            });

            // Add hidden input for allowances
            const allowanceInput = document.createElement('input');
            allowanceInput.type = 'hidden';
            allowanceInput.name = 'allowances';
            allowanceInput.value = JSON.stringify(allowances);
            this.appendChild(allowanceInput);

            // Process deductions
            const deductionNames = document.querySelectorAll('input[name="deduction_names[]"]');
            const deductionValues = document.querySelectorAll('input[name="deduction_values[]"]');
            const deductions = {};

            deductionNames.forEach((nameInput, index) => {
                const name = nameInput.value.trim();
                const value = parseFloat(deductionValues[index].value) || 0;
                if (name && value > 0) {
                    deductions[name] = value;
                }
            });

            // Add hidden input for deductions
            const deductionInput = document.createElement('input');
            deductionInput.type = 'hidden';
            deductionInput.name = 'deductions';
            deductionInput.value = JSON.stringify(deductions);
            this.appendChild(deductionInput);
        });
    });
</script>

@endsection