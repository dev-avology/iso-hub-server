<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JotForm extends Model
{
    use HasFactory;
    protected $table = 'jot_forms';
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'merchant_name',
        'iso_form_status',
        'description',
        'signature_date',
        'signature',
        'dba',
        'address2',
        'city',
        'state',
        'pincode',
        'is_same_shipping_address',
        'is_duplicate',
        'business_dba',
        'business_corporate_legal_name',
        'business_location_address',
        'business_corporate_address',
        'business_city',
        'business_state',
        'business_zip',
        'business_phone_number',
        'business_contact_name',
        'business_contact_number',
        'business_start_date',
        'business_tax_id',
        'business_profile_business_type',

        'bank_name',
        'aba_routing',
        'doa',
        'business_type',
        'processing_services',
        'terminal',
        'terminal_special_features',
        'terminal_type_or_model',
        'mobile_app',
        'mobile_app_special_features',
        'mobile_app_cardreader_type_model',
        'pos_point_of_sale',
        'pos_special_features',
        'system_type_model',
        'number_of_stations',
        'pos_other_items',
        'virtual_terminal',
        'business_type_other',
        'personal_guarantee_required',
        'clear_signature',
        'mail_status',
    ];

    protected $hidden = [
        "business_corporate_legal_name",
        "business_location_address",
        "business_corporate_address",
        "ownership_owner_name",
        "ownership_title",
        "ownership_percent",
        "ownership_phone_number",
        "ownership_city",
        "ownership_state",
        "ownership_zip",
        "ownership_email",
        "ownership_dob",
        "ownership_social_security_number",
        "ownership_residential_street_address",
        "ownership_driver_licence_number",
        "terminal_special_features",
        "first_name",
        "last_name",
        "description",
        "dba",
        "address2",
        "state",
        "city",
        "pincode",
        "business_phone_number",
        "business_contact_number",
    ];

    public function get_jotform_details()
    {
        return $this->hasMany(JotFormDetails::class, 'jot_form_id','id');
    }

    public function get_jotform_docs()
    {
        return $this->hasMany(UploadFiles::class, 'form_id','id');
    }

    public function get_jotform_owner_docs()
    {
        return $this->hasMany(JotFormOwnerDocs::class, 'jot_form_id','id');
    }
}
