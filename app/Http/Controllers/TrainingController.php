<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\Employee;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    public function index()
    {
        $trainings = Training::with('employee')->get();
        return view('admin.trainings', compact('trainings'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('admin.trainings.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:planned,ongoing,completed,cancelled',
            'description' => 'nullable|string',
        ]);

        $training = new Training();
        $training->employee_id = $request->employee_id;
        $training->title = $request->title;
        $training->category = $request->category;
        $training->start_date = $request->start_date;
        $training->end_date = $request->end_date;
        $training->status = $request->status;
        $training->description = $request->description;
        $training->save();

        return redirect()->route('trainings.index')->with('success', 'Training record created successfully.');
    }

    public function show(Training $training)
    {
        $training->load('employee');
        return view('admin.trainings.show', compact('training'));
    }

    public function edit(Training $training)
    {
        $employees = Employee::all();
        return view('admin.trainings.edit', compact('training', 'employees'));
    }

    public function update(Request $request, Training $training)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:planned,ongoing,completed,cancelled',
            'description' => 'nullable|string',
        ]);

        $training->employee_id = $request->employee_id;
        $training->title = $request->title;
        $training->category = $request->category;
        $training->start_date = $request->start_date;
        $training->end_date = $request->end_date;
        $training->status = $request->status;
        $training->description = $request->description;
        $training->save();

        return redirect()->route('trainings.index')->with('success', 'Training record updated successfully.');
    }

    public function destroy(Training $training)
    {
        $training->delete();
        return redirect()->route('trainings.index')->with('success', 'Training record deleted successfully.');
    }
}
