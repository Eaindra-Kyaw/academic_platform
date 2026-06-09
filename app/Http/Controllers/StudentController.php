<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function dashboard()
    {
        return view('student.dashboard');
    }

    public function attendance()
    {
        return view('student.attendance');
    }

    public function scan()
    {
        return view('student.scan');
    }

    public function timetable()
    {
        return view('student.timetable');
    }

    public function progress()
    {
        return view('student.progress');
    }
}
