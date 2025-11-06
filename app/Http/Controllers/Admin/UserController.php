<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'phone' => 'nullable|string',
            'role' => 'required|in:admin,user,owner,kasir'
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'User created successfully'
        ], 201);
    }

    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string',
            'role' => 'required|in:admin,user,owner,kasir'
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'User updated successfully'
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}