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
    public function create($request, $user_id)
    {
        $form = JotForm::create([
            'signature_date' => $request->signature_date,
            'signature' => $request->signature,
            'user_id' => $user_id,
            'is_duplicate' => isset($request->is_duplicate) ? '1' : '0',
            'business_type_other' => $request->business_type_other ?? '',

            'business_dba' => $request->business_dba ?? '',
            'business_corporate_legal_name' => $request->business_corporate_legal_name ?? '',
            'business_location_address' => $request->business_location_address ?? '',
            'business_corporate_address' => $request->business_corporate_address ?? '',
            'business_city' => $request->business_city ?? '',
            'business_state' => $request->business_state ?? '',
            'business_zip' => $request->business_zip ?? '',
            'business_phone_number' => $request->business_phone_number ?? '',
            'business_contact_name' => $request->business_contact_name ?? '',
            'business_contact_number' => $request->business_contact_number ?? '',
            'business_start_date' => $request->business_start_date ?? '',
            'business_tax_id' => $request->business_tax_id ?? '',
            'business_profile_business_type' => json_encode($request->business_profile_business_type) ?? '',

            'ownership_owner_name' => $request->ownership_owner_name ?? '',
            'ownership_title' => $request->ownership_title ?? '',
            'ownership_percent' => $request->ownership_percent ?? '',
            'ownership_phone_number' => $request->ownership_phone_number ?? '',
            'ownership_city' => $request->ownership_city ?? '',
            'ownership_state' => $request->ownership_state ?? '',
            'ownership_zip' => $request->ownership_zip ?? '',
            'ownership_email' => $request->ownership_email ?? '',
            'ownership_dob' => $request->ownership_dob ?? '',
            'ownership_social_security_number' => $request->ownership_social_security_number ?? '',
            'ownership_residential_street_address' => $request->ownership_residential_street_address ?? '',
            'ownership_driver_licence_number' => $request->ownership_driver_licence_number ?? '',

            'bank_name' => $request->bank_name ?? '',
            'aba_routing' => $request->aba_routing ?? '',
            'doa' => $request->doa ?? '',

            'business_type' => json_encode($request->business_type) ?? '',
            'processing_services' => json_encode($request->processing_services) ?? '',

            'terminal' => json_encode($request->terminal) ?? '',
            'terminal_special_features' => $request->terminal_special_features ?? '',
            'terminal_type_or_model' => $request->terminal_type_or_model ?? '',
            'mobile_app' => json_encode($request->mobile_app) ?? '',
            'mobile_app_special_features' => $request->mobile_app_special_features ?? '',
            'mobile_app_cardreader_type_model' => $request->mobile_app_cardreader_type_model ?? '',
            'pos_point_of_sale' => json_encode($request->pos_point_of_sale) ?? '',
            'pos_special_features' => $request->pos_special_features ?? '',
            'system_type_model' => $request->system_type_model ?? '',
            'number_of_stations' => $request->number_of_stations ?? '',
            'pos_other_items' => $request->pos_other_items ?? '',
            'virtual_terminal' => json_encode($request->virtual_terminal) ?? '',
        ]);

        return $form;
    }
}
