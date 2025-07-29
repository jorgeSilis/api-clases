<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $modules = Module::with('tutor')->get();

        $modules = [
            'response' => $modules,
            'error' => false
        ];

        return response()->json($modules, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'number_sessions' => 'required|integer|min:1',
            'tutor_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error validating data.',
                'error' => true,
                'errorMessage' => $validator->errors()
            ];

            return response()->json($data, 400);
        };

        //validamos que sea tutor
        $tutor = User::where('id', $request->tutor_id)
            ->where('user_type', 'tutor')
            ->first();

        if (!$tutor) {
            $data = [
                'message' => 'The user assigned as tutor is not a tutor.',
                'error' => true
            ];
            return response()->json($data, 400);
        }

        $module = Module::create([
            'name' => $request->name,
            'number_sessions' => $request->number_sessions,
            'tutor_id' => $request->tutor_id
        ]);

        return response()->json([
            'message' => 'Module created successfully.',
            'error' => false,
            'response' => $module
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $module = Module::with('tutor')->find($id);

        if (!$module) {
            $data = [
                'message' => 'Could not found that module!',
                'error' => true
            ];
            return response()->json($data, 404);
        }

        $data = [
            'response' => $module,
            'error' => false
        ];

        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $module = Module::find($id);

        if (!$module) {
            $data = [
                'message' => 'Could not found that module for update.',
                'error' => true
            ];
            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'number_sessions' => 'required|integer|min:1',
            'tutor_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error validating data.',
                'error' => true,
                'errorMessage' => $validator->errors()
            ];
            return response()->json($data, 400);
        }

        //validamos que sea tutor
        $tutor = User::where('id', $request->tutor_id)
            ->where('user_type', 'tutor')
            ->first();

        if (!$tutor) {
            $data = [
                'message' => 'The user assigned as tutor is not a tutor.',
                'error' => true
            ];
            return response()->json($data, 400);
        }

        $module->update([
            'name' => $request->name,
            'number_sessions' => $request->number_sessions,
            'tutor_id' => $request->tutor_id
        ]);

        $data = [
            'message' => 'Module updated succesfully!',
            'error' => false
        ];

        return response()->json($data, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $module = Module::find($id);

        if (!$module) {
            $data = [
                'message' => 'Could not found that module for delete.',
                'error' => true
            ];

            return response()->json($data, 404);
        }

        try {
            $module->delete();
            $data = [
                'message' => 'module deleted succesfully',
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
