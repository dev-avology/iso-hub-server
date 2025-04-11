<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jot_forms', function (Blueprint $table) {
            $table->string('business_dba')->nullable()->after('status');
            $table->string('business_corporate_legal_name')->nullable()->after('business_dba');
            $table->text('business_location_address')->nullable()->after('business_corporate_legal_name');
            $table->text('business_corporate_address')->nullable()->after('business_location_address');
            $table->string('business_city')->nullable()->after('business_corporate_address');
            $table->string('business_state')->nullable()->after('business_city');
            $table->string('business_zip')->nullable()->after('business_state');
            $table->string('business_phone_number')->nullable()->after('business_zip');
            $table->string('business_contact_name')->nullable()->after('business_phone_number');
            $table->string('business_contact_number')->nullable()->after('business_contact_name');
            $table->date('business_start_date')->nullable()->after('business_contact_number');
            $table->string('business_tax_id')->nullable()->after('business_start_date');
            $table->text('business_profile_business_type')->nullable()->after('business_tax_id');

            $table->string('ownership_owner_name')->nullable()->after('business_profile_business_type');
            $table->string('ownership_title')->nullable()->after('ownership_owner_name');
            $table->string('ownership_percent')->nullable()->after('ownership_title');
            $table->string('ownership_phone_number')->nullable()->after('ownership_percent');
            $table->string('ownership_city')->nullable()->after('ownership_phone_number');
            $table->string('ownership_state')->nullable()->after('ownership_city');
            $table->string('ownership_zip')->nullable()->after('ownership_state');
            $table->string('ownership_email')->nullable()->after('ownership_zip');
            $table->date('ownership_dob')->nullable()->after('ownership_email');
            $table->string('ownership_social_security_number')->nullable()->after('ownership_dob');
            $table->text('ownership_residential_street_address')->nullable()->after('ownership_social_security_number');
            $table->string('ownership_driver_licence_number')->nullable()->after('ownership_residential_street_address');

            $table->string('bank_name')->nullable()->after('ownership_driver_licence_number');
            $table->string('aba_routing')->nullable()->after('bank_name');
            $table->string('doa')->nullable()->after('aba_routing');

            $table->text('business_type')->nullable()->after('doa');            

            $table->text('processing_services')->nullable()->after('business_type');

            $table->text('terminal')->nullable()->after('processing_services');
            $table->string('terminal_special_features')->nullable()->after('terminal');
            $table->string('terminal_type_or_model')->nullable()->after('terminal_special_features');

            $table->text('mobile_app')->nullable()->after('terminal_type_or_model');
            $table->string('mobile_app_special_features')->nullable()->after('mobile_app');
            $table->string('mobile_app_cardreader_type_model')->nullable()->after('mobile_app_special_features');

            $table->text('pos_point_of_sale')->nullable()->after('mobile_app_cardreader_type_model');
            $table->string('pos_special_features')->nullable()->after('pos_point_of_sale');
            $table->string('system_type_model')->nullable()->after('pos_special_features');
            $table->string('number_of_stations')->nullable()->after('system_type_model');
            $table->string('pos_other_items')->nullable()->after('number_of_stations');

            $table->text('virtual_terminal')->nullable()->after('pos_other_items');
            $table->string('business_type_other')->nullable()->after('virtual_terminal');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jot_forms', function (Blueprint $table) {
            $table->dropColumn([
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
                'ownership_owner_name',
                'ownership_title',
                'ownership_percent',
                'ownership_phone_number',
                'ownership_city',
                'ownership_state',
                'ownership_zip',
                'ownership_email',
                'ownership_dob',
                'ownership_social_security_number',
                'ownership_residential_street_address',
                'ownership_driver_licence_number',
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
            ]);
        });
    }
};
