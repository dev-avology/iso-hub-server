<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\TeamMember;
use App\Models\Vendor;
use App\Models\Rep;
use App\Models\UploadFiles;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RepService
{
    public function addRep($request)
    {
        // Create the user
        $user = Rep::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'user_id' => $request->user_id,
        ]);
        return $user;
    }

    public function updateRep($request)
    {
        $user = Rep::where('id', $request->id)->first();
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'user_id' => $request->user_id,
        ]);
        return $user;
    }


    public function destroyRep($id)
    {
        // Find the user
        $user = Rep::findOrFail($id);
        // Delete the user
        $user->delete();
        return true;
    }
}
