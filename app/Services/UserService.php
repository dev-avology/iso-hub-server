<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\TeamMember;
use App\Models\Vendor;
use App\Models\UploadFiles;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    public function addTeamMember($request)
    {
        // Create the user
        $user = TeamMember::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);
        return $user;
    }

    public function addUser($request)
    {
        // Create the user
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_id' => $request->role_id,
            'password' => Hash::make($request->password), // Encrypt password
            'unique_string' => Str::random(32),
        ]);

        // Assign role to user
        $role = Role::where('id', $user->role_id)->first();
        if ($role) {
            $user->assignRole($role);
        }

        return $user;
    }

    public function addVendor($request)
    {
        // Create the user
        $user = Vendor::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);
        return $user;
    }

    public function updateTeamMember($request)
    {
        $user = TeamMember::where('id', $request->id)->first();
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);
        return $user;
    }

    public function updateUser($request)
    {
        $user = User::where('id', $request->id)->first();
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role_id' => $request->role_id
        ]);
        return $user;
    }

    public function updateVendor($request)
    {
        $user = Vendor::where('id', $request->id)->first();
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);
        return $user;
    }

    public function destroyTeamMember($id)
    {
        // Find the user
        $user = User::findOrFail($id);
        // Delete the user
        $user->delete();
        return true;
    }

    public function destroyVendor($id)
    {
        // Find the user
        $user = Vendor::findOrFail($id);
        // Delete the user
        $user->delete();
        return true;
    }

    public function destroyUser($id)
    {
        // Find the user
        $user = User::findOrFail($id);
        // Delete the user
        $user->delete();
        return true;
    }

    public function updateUserInfoWithPass($request){
        $updated_data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        $user = User::find($request->user_id);

        if(isset($request->new_password) && !empty($request->new_password)){
            $updated_data['password'] = Hash::make($request->new_password);
        }
        $user->update($updated_data);
        return true;
    }
}
