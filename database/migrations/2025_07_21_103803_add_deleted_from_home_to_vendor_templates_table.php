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
        Schema::table('vendor_templates', function (Blueprint $table) {
            $table->boolean('deleted_from_home')->default(0)->after('card_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_templates', function (Blueprint $table) {
           $table->dropColumn('deleted_from_home');
        });
    }
};
