<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Session;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attendances = Attendance::with('student')->get();

        $data = [
            'attendances' => $attendances,
            'error' => false
        ];
        return response()->json($data, 200);
    }


    public function getBySession($id)
    {
        $session = Session::find($id);

        if (!$session) {
            $data = [
                'message' => 'Session not found',
                'error' => true
            ];
            return response()->json($data, 404);
        }

        $students = $session->module->students;

        $attendanceDetails = $students->map(function ($student) use ($session) {
            $attendance = Attendance::where('student_id', $student->id)
                ->where('session_id', $session->id)
                ->first();

            $attendanceStatus = $attendance ? $attendance->status : 'absent';

            return [
                'student' => $student,
                'attendance_status' => $attendanceStatus
            ];
        });

        $data = [
            'attendance_details' => $attendanceDetails,
            'error' => false
        ];

        return response()->json($data, 200);
    }

    public function getSummaryByStudent($id)
    {
        $student = User::where('id', $id)->where('user_type', 'student')->first();

        if (!$student) {
            $data = [
                'message' => 'Student not found',
                'error' => true
            ];
            return response()->json($data, 404);
        }

        $summary = [];

        foreach ($student->user_modules as $module) {
            $totalSessions = $module->sessions->count();

            $attendedSessions = Attendance::where('student_id', $student->id)
                ->whereIn('session_id', $module->sessions->pluck('id'))
                ->where('status', 'present')
                ->count();

            $percentage = $totalSessions > 0 ? round(($attendedSessions / $totalSessions) * 100) : 0;

            $summary[] = [
                'module' => $module->name,
                'total_sessions' => $totalSessions,
                'attended' => $attendedSessions,
                'percentage' => $percentage
            ];
        }
        $data = [
            'attendance_summary' => $summary,
            'error' => false
        ];
        return response()->json($data, 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $sessionId)
    {
        $session = Session::find($sessionId);

        if (!$session) {
            $data = [
                'message' => 'Session not found',
                'error' => true
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:users,id',
            'attendances.*.status' => 'required|in:present,absent'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Validation failed',
                'error' => true,
                'errors' => $validator->errors()
            ];
            return response()->json($data, 400);
        }

        foreach ($request->input('attendances') as $attend) {
            Attendance::updateOrCreate(
                [
                    'session_id' => $session->id,
                    'student_id' => $attend['student_id']
                ],
                [
                    'status' => $attend['status']
                ]
            );
        }

        $data = [
            'message' => 'Attendances saved successfully',
            'error' => false
        ];

        return response()->json($data, 200);
    }
}
