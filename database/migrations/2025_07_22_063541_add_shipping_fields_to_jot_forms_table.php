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
            $table->string('shipping_street_address')->nullable()->after('is_same_shipping_address');
            $table->string('shipping_street_address2')->nullable()->after('shipping_street_address');
            $table->string('shipping_city')->nullable()->after('shipping_street_address2');
            $table->string('shipping_state')->nullable()->after('shipping_city');
            $table->string('shipping_zip')->nullable()->after('shipping_state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jot_forms', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_street_address',
                'shipping_street_address2',
                'shipping_city',
                'shipping_state',
                'shipping_zip'
            ]);
        });
    }
};
