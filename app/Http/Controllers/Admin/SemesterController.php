<?php
// app/Http/Controllers/Admin/SemesterController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SemesterController extends Controller
{
    public function index()
    {
        $semesters = Semester::orderBy('year')
            ->orderBy('semester')
            ->paginate(15);

        $stats = [
            'total' => Semester::count(),
            'active' => Semester::where('is_active', true)->count(),
            'current' => Semester::where('is_current', true)->first(),
            'by_year' => Semester::select('year', DB::raw('count(*) as total'))
                ->groupBy('year')
                ->orderBy('year')
                ->get()
                ->pluck('total', 'year')
                ->toArray(),
        ];

        return view('admin.semesters.index', compact('semesters', 'stats'));
    }

    public function create()
    {
        $yearNames = Semester::$yearNames;
        $semesterNames = Semester::$semesterNames;
        return view('admin.semesters.create', compact('yearNames', 'semesterNames'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|in:1,2',
            'academic_year' => 'nullable|string|max:20',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'nullable|boolean',
            'is_current' => 'nullable|boolean',
        ]);

        $exists = Semester::where('year', $validated['year'])
            ->where('semester', $validated['semester'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'This semester already exists!')->withInput();
        }

        if ($request->has('is_current')) {
            Semester::where('is_current', true)->update(['is_current' => false]);
        }

        $validated['code'] = 'Y' . $validated['year'] . 'S' . $validated['semester'];
        $validated['is_active'] = $request->has('is_active');
        $validated['is_current'] = $request->has('is_current');

        // Set default dates if not provided
        if (empty($validated['start_date']) || empty($validated['end_date'])) {
            $baseYear = 2024 + $validated['year'];

            if ($validated['semester'] == 1) {
                $validated['start_date'] = date('Y-m-d', strtotime($baseYear . '-12-01'));
                $validated['end_date'] = date('Y-m-d', strtotime(($baseYear + 1) . '-03-31'));
            } else {
                $validated['start_date'] = date('Y-m-d', strtotime(($baseYear + 1) . '-06-01'));
                $validated['end_date'] = date('Y-m-d', strtotime(($baseYear + 1) . '-09-30'));
            }
        }

        Semester::create($validated);

        return redirect()->route('admin.semesters.index')
            ->with('success', 'Semester created successfully!');
    }

    public function show($id)
    {
        $semester = Semester::findOrFail($id);

        // Get courses in this semester (matching year and semester)
        $courses = \App\Models\Course::where('year', $this->getYearName($semester->year))
            ->where('semester', $this->getSemesterName($semester->semester))
            ->with('department')
            ->get();

        return view('admin.semesters.show', compact('semester', 'courses'));
    }

    public function edit($id)
    {
        $semester = Semester::findOrFail($id);
        $yearNames = Semester::$yearNames;
        $semesterNames = Semester::$semesterNames;
        return view('admin.semesters.edit', compact('semester', 'yearNames', 'semesterNames'));
    }

    public function update(Request $request, $id)
    {
        $semester = Semester::findOrFail($id);

        $validated = $request->validate([
            'year' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|in:1,2',
            'academic_year' => 'nullable|string|max:20',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'nullable|boolean',
            'is_current' => 'nullable|boolean',
        ]);

        $exists = Semester::where('year', $validated['year'])
            ->where('semester', $validated['semester'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'This semester already exists!')->withInput();
        }

        if ($request->has('is_current')) {
            Semester::where('is_current', true)->update(['is_current' => false]);
        }

        $validated['code'] = 'Y' . $validated['year'] . 'S' . $validated['semester'];
        $validated['is_active'] = $request->has('is_active');
        $validated['is_current'] = $request->has('is_current');

        $semester->update($validated);

        return redirect()->route('admin.semesters.index')
            ->with('success', 'Semester updated successfully!');
    }

    public function destroy($id)
    {
        $semester = Semester::findOrFail($id);

        if ($semester->is_current) {
            return back()->with('error', 'Cannot delete the current semester!');
        }

        // Check if any courses are linked to this semester
        $courseCount = \App\Models\Course::where('year', $this->getYearName($semester->year))
            ->where('semester', $this->getSemesterName($semester->semester))
            ->count();

        if ($courseCount > 0) {
            return back()->with('error', "Cannot delete this semester because it has {$courseCount} courses!");
        }

        $semester->delete();

        return redirect()->route('admin.semesters.index')
            ->with('success', 'Semester deleted successfully!');
    }

    public function toggleStatus($id)
    {
        $semester = Semester::findOrFail($id);
        $semester->update(['is_active' => !$semester->is_active]);

        $status = $semester->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.semesters.index')
            ->with('success', "Semester {$status} successfully!");
    }

    public function setCurrent($id)
    {
        $semester = Semester::findOrFail($id);

        Semester::where('is_current', true)->update(['is_current' => false]);
        $semester->update(['is_current' => true]);

        return redirect()->route('admin.semesters.index')
            ->with('success', 'Semester set as current successfully!');
    }

    public function generate()
    {
        if (Semester::count() > 0) {
            return redirect()->route('admin.semesters.index')
                ->with('error', 'Semesters already exist! Please delete existing ones first.');
        }

        $semesters = [];
        $startYear = 2025;
        $academicYear = $startYear . '-' . ($startYear + 1);

        for ($year = 1; $year <= 6; $year++) {
            $baseYear = $startYear + $year - 1;

            // First Semester: December to March
            $semesters[] = [
                'year' => $year,
                'semester' => 1,
                'code' => 'Y' . $year . 'S1',
                'academic_year' => $academicYear,
                'start_date' => date('Y-m-d', strtotime($baseYear . '-12-01')),
                'end_date' => date('Y-m-d', strtotime(($baseYear + 1) . '-03-31')),
                'is_active' => true,
                'is_current' => ($year == 1),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Second Semester: June to September
            $semesters[] = [
                'year' => $year,
                'semester' => 2,
                'code' => 'Y' . $year . 'S2',
                'academic_year' => $academicYear,
                'start_date' => date('Y-m-d', strtotime(($baseYear + 1) . '-06-01')),
                'end_date' => date('Y-m-d', strtotime(($baseYear + 1) . '-09-30')),
                'is_active' => true,
                'is_current' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Semester::insert($semesters);

        return redirect()->route('admin.semesters.index')
            ->with('success', 'All 12 semesters generated successfully!');
    }

    private function getYearName($yearNumber)
    {
        $yearNames = [
            1 => 'First Year',
            2 => 'Second Year',
            3 => 'Third Year',
            4 => 'Fourth Year',
            5 => 'Fifth Year',
            6 => 'Sixth Year',
        ];
        return $yearNames[$yearNumber] ?? 'Unknown Year';
    }

    private function getSemesterName($semesterNumber)
    {
        $semesterNames = [
            1 => 'First Semester',
            2 => 'Second Semester',
        ];
        return $semesterNames[$semesterNumber] ?? 'Unknown Semester';
    }
}
