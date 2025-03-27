<?php

namespace App\Services;

use App\Models\JotForm;
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

class JotFormService
{
    public function create($request,$user_id)
    {
        $form = JotForm::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'description' => $request->description,
            'signature_date' => $request->signature_date,
            'signature' => $request->signature,
            'user_id' => $user_id
        ]);
        return $form;
    }
}
