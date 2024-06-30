<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        // Retrieve all users
        $users = User::all();
        return response()->json(['users' => $users], 200);
    }

    public function store(Request $request): JsonResponse
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'login' => 'required|string|unique:users',
            'email' => 'required|string|email:rfc,dns|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Create new user
        $user = User::create([
            'login' => $request->login,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }

    public function show(): JsonResponse
    {
        $user = Auth::user();
        $user->makeVisible(['email']);
        $user = UserResource::make($user);
        return response()->json(['user' => $user], 200);
    }

    public function update(Request $request, $id): JsonResponse
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'login' => 'string|unique:users',
            'email' => 'string|email:rfc,dns|unique:users',
            'password' => 'string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Find user by ID and update
        $user = User::findOrFail($id);

        if ($request->has('login')) {
            $user->login = $request->login;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
    }

    public function destroy($id): JsonResponse
    {
        // Delete a user by ID
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}

