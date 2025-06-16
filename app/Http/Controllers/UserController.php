<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        return response()->json(User::all());
    }

    /**
     * Store a new user.
     */
    public function store(Request $request)
    {
        echo "xyz";
        $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'required|string|unique:users,number',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6',
            'type' => 'nullable|in:photography,catering,banquet',
            'user_type' => 'required|in:vendor,user,admin',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'nullable|string',
        ]);

        $profilePicturePath = $request->hasFile('profile_picture') ? $request->file('profile_picture')->store('profile_pictures', 'public') : null;

        $user = User::create([
            'name' => $request->name,
            'number' => $request->number,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => $request->type,
            'user_type' => $request->user_type,
            'profile_picture' => $profilePicturePath,
            'description' => $request->description,
        ]);

        return response()->json($user, 201);
    }

    /**
     * Display a specific user.
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    /**
     * Update a user.
     */
    // public function update(Request $request, $id)
    // {
    //     $user = User::find($id);

    //     if (!$user) {
    //         return response()->json(['message' => 'User not found'], 404);
    //     }

    //     $request->validate([
    //         'name' => 'sometimes|string|max:255',
    //         'number' => 'sometimes|string|unique:users,number,' . $id,
    //         'email' => 'sometimes|string|email|unique:users,email,' . $id,
    //         'password' => 'sometimes|string|min:6',
    //         'type' => 'nullable|in:photography,catering,banquet',
    //         'user_type' => 'sometimes|in:vendor,user,admin',
    //         'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //         'description' => 'nullable|string',
    //     ]);

    //     $updateData = $request->only([
    //         'name', 'number', 'email', 'type', 'user_type', 'description'
    //     ]);

    //     if ($request->hasFile('profile_picture')) {
    //         if ($user->profile_picture) {
    //             Storage::disk('public')->delete($user->profile_picture);
    //         }
    //         $file = $request->file('profile_picture');
    //         $filePath = $file->store('profile_pictures', 'public');
    //         $updateData['profile_picture'] = $filePath;
    //     }

    //     if ($request->filled('password')) {
    //         $updateData['password'] = Hash::make($request->password);
    //     }

    //     $user->update($updateData);

    //     return response()->json([
    //         'message' => 'User updated successfully',
    //         'user' => $user,
    //         'profile_picture_url' => $user->profile_picture ? asset('storage/' . $user->profile_picture) : null
    //     ]);
    // }
public function update(Request $request, $id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $request->validate([
        'name'            => 'sometimes|string|max:255',
        'phone'           => 'sometimes|string|unique:users,phone,' . $id,
        'email'           => 'sometimes|email|unique:users,email,' . $id,
        'username'        => 'sometimes|string|unique:users,username,' . $id,
        'password'        => 'sometimes|string|min:6',
        'otp'             => 'required_with:password|string|max:6',
        'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $updateData = $request->only([
        'name', 'phone', 'email', 'username'
    ]);

    // Validate OTP if password is being updated
    if ($request->filled('password')) {
        $otp = \App\Models\Otp::where('email', $user->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            return response()->json(['message' => 'Invalid or expired OTP'], 401);
        }

        $updateData['password'] = \Illuminate\Support\Facades\Hash::make($request->password);

        // Delete OTP after successful use
        $otp->delete();
    }

    // Handle profile picture update
    if ($request->hasFile('profile_picture')) {
        if ($user->profile_picture) {
            \Storage::disk('public')->delete($user->profile_picture);
        }

        $filePath = $request->file('profile_picture')->store('profile_pictures', 'public');
        $updateData['profile_picture'] = $filePath;
    }

    $user->update($updateData);

    return response()->json([
        'message' => 'User updated successfully',
        'user' => $user,
        'profile_picture_url' => $user->profile_picture ? asset('storage/' . $user->profile_picture) : null
    ]);
}



    /**
     * Delete a user.
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
     public function login(Request $request)
    {
        $request->validate([
            'email'     => 'required|email',
            'password'  => 'required|string',
            'user_type' => 'required|in:vendor,user,admin',
        ]);

        $user = User::where('email', $request->email)
            ->where('user_type', $request->user_type)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials or user type'], 401);
        }

        // Add token generation if needed (e.g., Laravel Sanctum or Passport)

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'profile_picture_url' => $user->profile_picture ? asset('storage/' . $user->profile_picture) : null
        ]);
    }
}
