<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MachineLearning\AttendancePredictor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MLPredictionController extends Controller
{
    protected $predictor;

    public function __construct(AttendancePredictor $predictor)
    {
        $this->predictor = $predictor;
    }

    /**
     * Get risk prediction for current student
     */
    public function predictStudent(Request $request)
    {
        $student = Auth::user();

        if (!$student || $student->role_id != 3) {
            return response()->json([
                'success' => false,
                'message' => 'Only students can access this endpoint.'
            ], 403);
        }

        $courseId = $request->input('course_id');

        $result = $this->predictor->predictRisk($student->id, $courseId);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Get batch predictions (admin only)
     */
    public function batchPredict(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role_id != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Only admin can access this endpoint.'
            ], 403);
        }

        $departmentId = $request->input('department_id');
        $courseId = $request->input('course_id');

        $students = User::where('role_id', 3);
        if ($departmentId) {
            $students->where('department_id', $departmentId);
        }
        $studentIds = $students->pluck('id')->toArray();

        $results = $this->predictor->batchPredict($studentIds, $courseId);

        return response()->json([
            'success' => true,
            'total' => count($results),
            'data' => $results,
        ]);
    }

    /**
     * Get model metrics
     */
    public function modelMetrics()
    {
        return response()->json([
            'success' => true,
            'data' => $this->predictor->getModelMetrics(),
        ]);
    }
}
