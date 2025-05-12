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
       Schema::create('jot_form_owner_docs', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('jot_form_id')->nullable()->constrained('jot_forms')->onDelete('cascade'); // Foreign key referencing jot_forms table
            $table->string('ownership_first_name')->nullable();
            $table->string('ownership_last_name')->nullable();
            $table->string('ownership_percent')->nullable();
            $table->string('ownership_phone_number')->nullable();
            $table->string('ownership_city')->nullable();
            $table->string('ownership_state')->nullable();
            $table->string('ownership_zip')->nullable();
            $table->string('ownership_email')->nullable();
            $table->string('ownership_dob')->nullable();
            $table->string('ownership_social_security_number')->nullable();
            $table->string('ownership_residential_street_address')->nullable();
            $table->string('ownership_driver_licence_number')->nullable();
            $table->text('ownership_address')->nullable();
            $table->string('owner_street_address')->nullable();
            $table->string('owner_street_address2')->nullable();
            $table->string('ownership_title')->nullable();
            $table->string('name')->nullable(); // Image name
            $table->string('path')->nullable(); // Image path
            $table->timestamps(); // Timestamps for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jot_form_owner_docs');
    }
};
