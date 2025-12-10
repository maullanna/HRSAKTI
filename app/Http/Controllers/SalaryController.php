<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\Employee;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function index()
    {
        $salaries = Salary::with('employee')->get();
        return view('admin.salaries.index', compact('salaries'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('admin.salaries.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id_employees',
            'month' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|array',
            'deductions' => 'nullable|array',
        ]);

        $salary = new Salary();
        $salary->employee_id = $request->employee_id;
        $salary->month = $request->month;
        $salary->basic_salary = $request->basic_salary;
        $salary->allowances = $request->allowances ?? [];
        $salary->deductions = $request->deductions ?? [];

        // Calculate net salary
        $totalAllowances = array_sum($salary->allowances ?? []);
        $totalDeductions = array_sum($salary->deductions ?? []);
        $salary->net_salary = $salary->basic_salary + $totalAllowances - $totalDeductions;

        $salary->save();

        return redirect()->route('salaries.index')->with('success', 'Salary record created successfully.');
    }

    public function show(Salary $salary)
    {
        $salary->load('employee');
        return view('admin.salaries.show', compact('salary'));
    }

    public function edit(Salary $salary)
    {
        $employees = Employee::all();
        return view('admin.salaries.edit', compact('salary', 'employees'));
    }

    public function update(Request $request, Salary $salary)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id_employees',
            'month' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|array',
            'deductions' => 'nullable|array',
        ]);

        $salary->employee_id = $request->employee_id;
        $salary->month = $request->month;
        $salary->basic_salary = $request->basic_salary;
        $salary->allowances = $request->allowances ?? [];
        $salary->deductions = $request->deductions ?? [];

        // Calculate net salary
        $totalAllowances = array_sum($salary->allowances ?? []);
        $totalDeductions = array_sum($salary->deductions ?? []);
        $salary->net_salary = $salary->basic_salary + $totalAllowances - $totalDeductions;

        $salary->save();

        return redirect()->route('salaries.index')->with('success', 'Salary record updated successfully.');
    }

    public function destroy(Salary $salary)
    {
        $salary->delete();
        return redirect()->route('salaries.index')->with('success', 'Salary record deleted successfully.');
    }
}
