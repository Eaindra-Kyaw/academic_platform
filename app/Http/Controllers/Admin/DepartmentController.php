<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     */
    public function index()
    {
        $departments = Department::orderBy('name')->get();
        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new department.
     */
    public function create()
    {
        return view('admin.departments.create');
    }

    /**
     * Store a newly created department in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:departments',
            'name' => 'required|string|max:100|unique:departments',
            'head_of_department' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        Department::create($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully!');
    }

    /**
     * Display the specified department.
     */
    public function show(Department $department)
    {
        return view('admin.departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Update the specified department in storage.
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:departments,code,' . $department->id,
            'name' => 'required|string|max:100|unique:departments,name,' . $department->id,
            'head_of_department' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $department->update($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully!');
    }

    /**
     * Remove the specified department from storage.
     */
    public function destroy(Department $department)
    {
        // Check if department has courses
        if ($department->courses()->count() > 0) {
            return redirect()->route('admin.departments.index')
                ->with('error', 'Cannot delete department with associated courses. Delete courses first.');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully!');
    }
}
