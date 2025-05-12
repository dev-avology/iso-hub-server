<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JotFormDetails extends Model
{
    use HasFactory;
    protected $table = 'jot_form_details';
    protected $fillable = [
        'jot_form_id',
        'dba_street_address',
        'dba_street_address2',
        'business_profile_business_type_other',
        'corporate_street_address1',
        'corporate_street_address2',
        'corporate_city',
        'corporate_state',
        'corporate_zip',
        'business_contact_mail',
        'business_location_phone_number',
        'business_date_started',
        'business_website',
        'business_legal_name',
        'terminal_other',
        'estimation_early_master_card',
        'estimated_average_ticket',
        'estimated_highest_ticket',
        'transaction_card_present',
        'transaction_keyed_in',
        'transaction_all_online',
        'auto_settle_time',
        'auto_settle_type',
        'add_tips_to_account',
        'tip_amounts',
        'business_products_sold',
        'business_return_policy',
        'location_description'
    ];
    
}
