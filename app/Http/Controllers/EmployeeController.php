<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Role;
use App\Models\Schedule;
use App\Http\Requests\EmployeeRec;

class EmployeeController extends Controller
{
   
    public function index()
    {
        
        return view('admin.master-data.employees.index')->with(['employees'=> Employee::all()]);
    }

    public function create()
    {
        return view('admin.master-data.employees.add_employee');
    }

    public function show(Employee $employee)
    {
        return view('admin.master-data.employees.edit_delete_employee', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        return view('admin.master-data.employees.edit_delete_employee', compact('employee'));
    }

    public function store(EmployeeRec $request)
    {
        $request->validated();

        $employee = new Employee;
        $employee->name = $request->name;
        $employee->employee_code = $request->employee_code;
        $employee->position = $request->position;
        $employee->email = $request->email;
        $employee->pin_code = bcrypt($request->pin_code);
        $employee->save();

        return redirect()->route('employees.index')->with('success', 'Employee Record has been created successfully !');
    }

 
    public function update(EmployeeRec $request, Employee $employee)
    {
        $request->validated();

        $employee->name = $request->name;
        $employee->employee_code = $request->employee_code;
        $employee->position = $request->position;
        $employee->email = $request->email;
        $employee->pin_code = bcrypt($request->pin_code);
        $employee->save();

        return redirect()->route('employees.index')->with('success', 'Employee Record has been Updated successfully !');
    }


    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee Record has been Deleted successfully !');
    }
}
