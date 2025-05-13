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
        Schema::create('jot_form_details', function (Blueprint $table) {
            $table->id(); // Primary key

            $table->unsignedBigInteger('jot_form_id')->nullable();

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
            // $table->string('ownership_first_name')->nullable();
            // $table->string('ownership_last_name')->nullable();
            // $table->string('owner_street_address')->nullable();
            // $table->string('owner_street_address2')->nullable();
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
            $table->text('business_products_sold')->nullable();
            $table->text('business_return_policy')->nullable();
            $table->text('location_description')->nullable();
            $table->timestamps(); // Timestamps for created_at and updated_at
            $table->foreign('jot_form_id')->references('id')->on('jot_forms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jot_form_details');
    }
};
