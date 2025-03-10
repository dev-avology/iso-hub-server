<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    public function addUser($request)
    {
        // Generate a random password
        $randomPassword = Str::random(10);

        // Create the user
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($randomPassword), // Store hashed password
            'role_id' => $request->role_id, // Assuming 'role' is not a hashed field
        ]);

        // Optionally, send the generated password via email
        // Mail::to($user->email)->send(new UserPasswordMail($user, $randomPassword));
        return $user;
    }

    public function updateUser($request)
    {
        $user = User::where('id', $request->user_id)->first();
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_id' => $request->role_id,
        ]);
        // Optionally, send the generated password via email
        // Mail::to($user->email)->send(new UserPasswordMail($user, $randomPassword));
        return $user;
    }

    public function destroyUser($user_id)
    {
        // Find the user
        $user = User::findOrFail($user_id);
        // Delete the user
        $user->delete();
        return true;
    }
}
