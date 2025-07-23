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
          $table->integer('is_dropdown_show')->default(0)->after('deleted_from_home');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_templates', function (Blueprint $table) {
          $table->dropColumn('is_dropdown_show');
        });
    }
};
