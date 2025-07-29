<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Session;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $session = Session::all();

        $data = [
            'sessions' => $session->students,
            'error' => false
        ];
        return response()->json($data, 200);
    }

    public function getByModule($id)
    {
        $module = Module::find($id);

        if (!$module) {
            $data = [
                'message' => 'Module not found',
                'error' => true
            ];
            return response()->json($data, 404);
        }

        $sessions = $module->sessions;

        $data = [
            'response' => $sessions,
            'error' => false
        ];

        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $moduleId)
    {
        $module = Module::find($moduleId);

        if (!$module) {
            $data = [
                'message' => 'Module not found',
                'error' => true
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Validation failed',
                'error' => true,
                'errorMessages' => $validator->errors()
            ];
            return response()->json($data, 400);
        }

        $session = $module->sessions()->create($validator->validated());

        $data = [
            'message' => 'Session created successfully',
            'error' => false,
            'response' => $session
        ];

        return response()->json($data, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $session = Session::find($id);

        if (!$session) {
            $data = [
                'message' => 'Session not found',
                'error' => true
            ];
            return response()->json($data, 404);
        }
        $data = [
            'response' => $session,
            'error' => false
        ];
        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $session = Session::find($id);

        if (!$session) {
            $data = [
                'message' => 'Session not found',
                'error' => true
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Validation failed',
                'error' => true,
                'errorMessages' => $validator->errors()
            ];

            return response()->json($data, 400);
        }

        $session->update($validator->validated());

        $data = [
            'message' => 'Session updated successfully',
            'error' => false
        ];
        return response()->json($data, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $session = Session::find($id);

        if (!$session) {
            return response()->json([
                'message' => 'Session not found',
                'error' => true
            ], 404);
        }

        try {
            $session->delete();
            $data = [
                'message' => 'Session deleted successfully',
                'error' => false
            ];
            return response()->json($data, 200);
        } catch (QueryException $e) {
            $data = [
                'message' => 'An unexpected database error occurred.',
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];
            return response()->json($data, 500);
        }
    }
}
