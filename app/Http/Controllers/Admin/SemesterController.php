<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SemesterController extends Controller
{
    /**
     * Display a listing of semesters
     */
    public function index()
    {
        // ✅ FIXED: Use 'academic_year' instead of 'year'
        $semestersByYear = Semester::select('academic_year', DB::raw('count(*) as total'))
            ->groupBy('academic_year')
            ->orderBy('academic_year', 'asc')
            ->get()
            ->pluck('total', 'academic_year');

        $semesters = Semester::orderBy('academic_year', 'desc')
            ->orderBy('semester_number', 'asc')
            ->get();

        $totalSemesters = Semester::count();
        $activeSemesters = Semester::where('is_active', true)->count();
        $currentSemester = Semester::where('is_current', true)->first();

        return view('admin.semesters.index', compact(
            'semesters',
            'semestersByYear',
            'totalSemesters',
            'activeSemesters',
            'currentSemester'
        ));
    }

    /**
     * Show the form for creating a new semester
     */
    public function create()
    {
        return view('admin.semesters.create');
    }

    /**
     * Store a newly created semester
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year' => 'required|string|max:255',
            'semester_number' => 'required|integer|min:1|max:3',
            'semester_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        // If this semester is set as current, unset any other current
        if ($request->has('is_current') && $request->is_current) {
            Semester::where('is_current', true)->update(['is_current' => false]);
        }

        $semester = Semester::create([
            'academic_year' => $validated['academic_year'],
            'semester_number' => $validated['semester_number'],
            'semester_name' => $validated['semester_name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_current' => $request->has('is_current') ? (bool) $request->is_current : false,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
            'year_name' => $this->getYearName($validated['academic_year']),
        ]);

        return redirect()->route('admin.semesters.index')
            ->with('success', 'Semester created successfully!');
    }

    /**
     * Display the specified semester
     */
    public function show($id)
    {
        $semester = Semester::findOrFail($id);
        return view('admin.semesters.show', compact('semester'));
    }

    /**
     * Show the form for editing a semester
     */
    public function edit($id)
    {
        $semester = Semester::findOrFail($id);
        return view('admin.semesters.edit', compact('semester'));
    }

    /**
     * Update the specified semester
     */
    public function update(Request $request, $id)
    {
        $semester = Semester::findOrFail($id);

        $validated = $request->validate([
            'academic_year' => 'required|string|max:255',
            'semester_number' => 'required|integer|min:1|max:3',
            'semester_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        // If this semester is set as current, unset any other current
        if ($request->has('is_current') && $request->is_current) {
            Semester::where('is_current', true)->where('id', '!=', $id)->update(['is_current' => false]);
        }

        $semester->update([
            'academic_year' => $validated['academic_year'],
            'semester_number' => $validated['semester_number'],
            'semester_name' => $validated['semester_name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_current' => $request->has('is_current') ? (bool) $request->is_current : false,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
            'year_name' => $this->getYearName($validated['academic_year']),
        ]);

        return redirect()->route('admin.semesters.index')
            ->with('success', 'Semester updated successfully!');
    }

    /**
     * Remove the specified semester
     */
    public function destroy($id)
    {
        $semester = Semester::findOrFail($id);
        $semester->delete();

        return redirect()->route('admin.semesters.index')
            ->with('success', 'Semester deleted successfully!');
    }

    /**
     * Toggle active status
     */
    public function toggleStatus($id)
    {
        $semester = Semester::findOrFail($id);
        $semester->is_active = !$semester->is_active;
        $semester->save();

        $status = $semester->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.semesters.index')
            ->with('success', "Semester {$status} successfully!");
    }

    /**
     * Set a semester as current
     */
    public function setCurrent($id)
    {
        // Unset all other current semesters
        Semester::where('is_current', true)->update(['is_current' => false]);

        $semester = Semester::findOrFail($id);
        $semester->is_current = true;
        $semester->save();

        return redirect()->route('admin.semesters.index')
            ->with('success', 'Semester set as current successfully!');
    }

    /**
     * Generate a year name from academic year
     */
    private function getYearName($academicYear)
    {
        // Format: "2025-2026" -> "2025-2026"
        return $academicYear;
    }

    /**
     * Generate semesters for a new academic year
     */
    public function generate(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $nextYear = $year + 1;

        $academicYear = $year . '-' . $nextYear;

        // Check if semesters already exist for this year
        $existing = Semester::where('academic_year', $academicYear)->count();
        if ($existing > 0) {
            return redirect()->back()->with('error', "Semesters for {$academicYear} already exist!");
        }

        // Create First Semester
        Semester::create([
            'academic_year' => $academicYear,
            'semester_number' => 1,
            'semester_name' => 'First Semester',
            'start_date' => Carbon::create($year, 6, 1),
            'end_date' => Carbon::create($year, 10, 31),
            'is_current' => false,
            'is_active' => true,
            'year_name' => $academicYear,
        ]);

        // Create Second Semester
        Semester::create([
            'academic_year' => $academicYear,
            'semester_number' => 2,
            'semester_name' => 'Second Semester',
            'start_date' => Carbon::create($year, 11, 1),
            'end_date' => Carbon::create($nextYear, 3, 31),
            'is_current' => false,
            'is_active' => true,
            'year_name' => $academicYear,
        ]);

        // Create Summer Semester (optional)
        Semester::create([
            'academic_year' => $academicYear,
            'semester_number' => 3,
            'semester_name' => 'Summer Semester',
            'start_date' => Carbon::create($nextYear, 4, 1),
            'end_date' => Carbon::create($nextYear, 5, 31),
            'is_current' => false,
            'is_active' => false,
            'year_name' => $academicYear,
        ]);

        return redirect()->route('admin.semesters.index')
            ->with('success', "Semesters for {$academicYear} generated successfully!");
    }
}
