<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRec extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $employee = $this->route('employee');
        $employeeId = $employee ? $employee->id_employees : null;
        
        return [
            'name' => 'required|string|min:3|max:64',
            'employee_code' => 'required|string|min:3|max:20|unique:employees,employee_code,' . $employeeId . ',id_employees',
            'position' => 'required|string|in:Wadir 1,Wadir 2,Section Prodi TPMO,Section Prodi TOPKR4,Section BAAK,Section Teaching Factory,Section IT & Sarpras,Section Administrasi,YTI Board of Directors,Employees,Magang,PKL',
            'email' => 'nullable|email|unique:employees,email,' . $employeeId . ',id_employees',
            'pin_code' => 'nullable|string|min:4|max:10',
            'nik' => 'nullable|string|max:20',
            'tanggal_lahir' => 'nullable|date',
            'pendidikan' => 'nullable|string|in:SD,SMP,SMA/SMK,D1,D2,D3,D4,S1,S2,S3',
            'kontrak_kerja' => 'nullable|string|in:Tetap,Kontrak,Magang,PKL,Freelance',
            'kontrak_durasi' => [
                'nullable',
                'required_if:kontrak_kerja,Magang,Kontrak,PKL,Freelance',
                'integer',
                'min:1',
                'max:120'
            ],
            'hire_date' => [
                'nullable',
                'required_if:kontrak_kerja,Magang,Kontrak,PKL,Freelance',
                'date'
            ],
            'phone' => 'nullable|string|max:20',
            'status' => 'nullable|in:active,inactive',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Employee name is required.',
            'name.min' => 'Employee name must be at least 3 characters.',
            'name.max' => 'Employee name must not exceed 64 characters.',
            'employee_code.required' => 'Employee code is required.',
            'employee_code.min' => 'Employee code must be at least 3 characters.',
            'employee_code.max' => 'Employee code must not exceed 20 characters.',
            'employee_code.unique' => 'This employee code is already registered.',
            'position.required' => 'Please select a position.',
            'position.in' => 'Please select a valid position from the list.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'pin_code.min' => 'PIN code must be at least 4 characters.',
            'pin_code.max' => 'PIN code must not exceed 10 characters.',
            'hire_date.required' => 'Tanggal mulai kontrak wajib diisi.',
            'hire_date.date' => 'Format tanggal tidak valid.',
            'kontrak_durasi.required_if' => 'Durasi kontrak wajib diisi untuk kontrak kerja ini.',
            'kontrak_durasi.integer' => 'Durasi kontrak harus berupa angka.',
            'kontrak_durasi.min' => 'Durasi kontrak minimal 1 bulan.',
            'kontrak_durasi.max' => 'Durasi kontrak maksimal 120 bulan.',
        ];
    }
}
