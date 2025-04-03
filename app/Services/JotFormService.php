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
            'dba' => $request->dba,
            'description' => $request->description,
            'address2' => $request->address2,
            'state' => $request->state,
            'city' => $request->city,
            'pincode' => $request->pincode,
            'is_same_shipping_address' => $request->is_same_shipping_address,
            'signature_date' => $request->signature_date,
            'signature' => $request->signature,
            'user_id' => $user_id,
            'is_duplicate' => isset($request->is_duplicate) ? '1' : '0' 
        ]);
        return $form;
    }
}
