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
            $table->string('merchant_name')->nullable()->after('last_name');
            $table->integer('iso_form_status')->nullable()->after('merchant_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jot_forms', function (Blueprint $table) {
           $table->dropColumn('merchant_name');
           $table->dropColumn('iso_form_status');
        });
    }
};
