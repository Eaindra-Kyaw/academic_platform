<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LecturerController extends Controller
{
    public function dashboard()
    {
        return view('lecturer.dashboard');
    }

    public function attendance()
    {
        return view('lecturer.attendance');
    }

    public function students()
    {
        return view('lecturer.students');
    }

    public function schedule()
    {
        return view('lecturer.schedule');
    }

    public function reports()
    {
        return view('lecturer.reports');
    }
}
