<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $module = Module::find($id);

        if (!$module) {
            $data = [
                'message' => 'Module not found',
                'error' => true
            ];
            return response()->json($data, 404);
        }
        $data = [
            'students' => $module->students,
            'error' => false
        ];
        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $module = Module::find($id);

        if (!$module) {
            $data = [
                'message' => 'Module not found',
                'error' => true
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Validation failed',
                'error' => true,
                'errorMessages' => $validator->errors()
            ];
            return response()->json($data, 400);
        }

        $studentIds = $request->input('student_ids');

        // only can enroll students validation
        $students = User::whereIn('id', $studentIds)
            ->where('user_type', 'student')
            ->pluck('id')
            ->toArray();

        $module->students()->syncWithoutDetaching($students);

        $data = [
            'message' => 'Students assigned to module',
            'error' => false
        ];
        
        return response()->json($data, 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, $student_id)
    {
        $module = Module::find($id);

        if (!$module) {
            $data = [
                'message' => 'Module not found',
                'error' => true
            ];
            return response()->json($data, 404);
        }

        $module->students()->detach($student_id);

        $data = [
            'message' => 'Student removed from module',
            'error' => false
        ];

        return response()->json($data, 200);
    }
}
