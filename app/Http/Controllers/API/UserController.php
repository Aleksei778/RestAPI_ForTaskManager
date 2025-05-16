<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function index() {
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'message' => 'Admin access required',
            ], 403);
        }
        
        $users = User::all();

        return UserResource::collection($users);
    }

    public function profile() {
        return new UserResource(auth()->user());
    }

    public function update(Request $request) {
        $user = auth()->user();

        $request->validate([
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        $userData = $request->only(['email']);

        if ($request->has('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return response()->json([
            'message'=> 'User updated successfully',
            'user' => new UserResource($user),
        ]);
    }

    public function destroy(User $user) {
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'message' => 'Admin access required',
            ], 403);
        }

        if ($user->id === auth()->user()->id) {
            return response()->json([
                'message' => 'Can\'t delete yourself',
            ], 403);
        }
        
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}
