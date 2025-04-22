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
            $table->string('personal_guarantee_required')->nullable()->after('virtual_terminal');
            $table->string('clear_signature')->nullable()->after('personal_guarantee_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jot_forms', function (Blueprint $table) {
            $table->dropColumn(['personal_guarantee_required', 'clear_signature']);
        });
    }
};
