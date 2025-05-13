<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jot_forms', function (Blueprint $table) {
            // Add nullable string fields
            $table->string('dba_street_address')->nullable();
            $table->string('dba_street_address2')->nullable();
            $table->string('business_profile_business_type_other')->nullable();
            $table->string('corporate_street_address1')->nullable();
            $table->string('corporate_street_address2')->nullable();
            $table->string('corporate_city')->nullable();
            $table->string('corporate_state')->nullable();
            $table->string('corporate_zip')->nullable();
            $table->string('business_contact_mail')->nullable();
            $table->string('business_location_phone_number')->nullable();
            $table->string('business_date_started')->nullable();
            $table->string('business_website')->nullable();
            $table->string('business_legal_name')->nullable();
            $table->string('ownership_first_name')->nullable();
            $table->string('ownership_last_name')->nullable();
            $table->string('owner_street_address')->nullable();
            $table->string('owner_street_address2')->nullable();
            $table->string('terminal_other')->nullable();
            $table->string('estimation_early_master_card')->nullable();
            $table->string('estimated_average_ticket')->nullable();
            $table->string('estimated_highest_ticket')->nullable();
            $table->string('transaction_card_present')->nullable();
            $table->string('transaction_keyed_in')->nullable();
            $table->string('transaction_all_online')->nullable();
            $table->string('auto_settle_time')->nullable();
            $table->string('auto_settle_type')->nullable();
            $table->string('add_tips_to_account')->nullable();
            $table->string('tip_amounts')->nullable();

            // Add nullable text fields
            $table->text('business_products_sold')->nullable();
            $table->text('business_return_policy')->nullable();
            $table->text('location_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jot_forms', function (Blueprint $table) {
            $table->dropColumn([
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
                'business_products_sold',
                'business_return_policy',
                'ownership_first_name',
                'ownership_last_name',
                'owner_street_address',
                'owner_street_address2',
                'terminal_other',
                'location_description',
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
            ]);
        });
    }
};
