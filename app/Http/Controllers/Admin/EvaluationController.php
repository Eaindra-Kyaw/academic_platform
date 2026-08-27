<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    public function index()
    {
        return view('admin.evaluations.index');
    }

    public function create()
    {
        return view('admin.evaluations.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.evaluations.index')->with('success', 'Evaluation created!');
    }

    public function show($id)
    {
        return view('admin.evaluations.show', ['id' => $id]);
    }

    public function edit($id)
    {
        return view('admin.evaluations.edit', ['id' => $id]);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.evaluations.index')->with('success', 'Evaluation updated!');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.evaluations.index')->with('success', 'Evaluation deleted!');
    }

    public function toggleStatus($id)
    {
        return redirect()->route('admin.evaluations.index')->with('success', 'Status toggled!');
    }

    public function generateResults($id)
    {
        return redirect()->route('admin.evaluations.index')->with('success', 'Results generated!');
    }

    public function sendResults($id)
    {
        return redirect()->route('admin.evaluations.index')->with('success', 'Results sent!');
    }

    public function sendToStudents($id)
    {
        return redirect()->route('admin.evaluations.index')->with('success', 'Sent to students!');
    }

    public function getStudentCount($id)
    {
        return response()->json(['count' => 0]);
    }

    public function status($id)
    {
        return response()->json(['status' => 'active']);
    }

    // Student facing methods
    public function studentIndex()
    {
        return view('student.evaluations.index');
    }

    public function studentShow($id)
    {
        return view('student.evaluations.show', ['id' => $id]);
    }

    public function submit(Request $request)
    {
        return redirect()->route('student.evaluations.index')->with('success', 'Evaluation submitted!');
    }
}
