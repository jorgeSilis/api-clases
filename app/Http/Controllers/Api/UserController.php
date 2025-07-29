<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Get all the users
     */
    public function index(Request $request)
    {
        $type = $request->query('type');

        if ($type) {
            if (!in_array($type, ['student', 'tutor'])) {
                $data = [
                    'message' => 'Invalid user type',
                    'error' => true
                ];
                return response()->json($data, 400);
            }

            $users = User::where('user_type', $type)->get();
        } else {
            $users = User::all();
        }

        $data = [
            'users' => $users,
            'error' => false
        ];

        return response()->json($data, 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'age' => 'nullable|integer|min:0',
            'email' => 'required|email|unique:users',
            'user_type' => 'required|in:student,tutor',
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error validating data.',
                'error' => true,
                'errorMessage' => $validator->errors()
            ];

            return response()->json($data, 400);
        }



        $User = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'age' => $request->age,
            'email' => $request->email,
            'user_type' => $request->user_type,
        ]);

        if (!$User) {
            $data = [
                'message' => 'Error creating a User.',
                'error' => true
            ];

            return response()->json($data, 500);
        }

        $data = [
            'message' => 'User created succesfully',
            'error' => false
        ];

        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            $data = [
                'message' => 'Could not found that user!',
                'error' => true
            ];
            return response()->json($data, 404);
        }

        $data = [
            'response' => $user,
            'error' => false
        ];

        return response()->json($data, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            $data = [
                'message' => 'Could not found that user for update.',
                'error' => true
            ];

            return response()->json($data, 404);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'age' => 'nullable|integer|min:0',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'user_type' => 'required|in:student,tutor'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error validating data.',
                'error' => true,
                'errorMessage' => $validator->errors()
            ];

            return response()->json($data, 400);
        }

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'age' => $request->age,
            'email' => $request->email,
            'user_type' => $request->user_type,
        ]);

        $data = [
            'message' => 'user updated succesfully!',
            'error' => false
        ];

        return response()->json($data, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            $data = [
                'message' => 'Could not found that user for delete.',
                'error' => true
            ];

            return response()->json($data, 404);
        }

        try {
            $user->delete();
            $data = [
                'message' => 'User deleted succesfully',
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
