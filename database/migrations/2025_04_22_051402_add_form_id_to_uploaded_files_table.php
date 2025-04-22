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
        Schema::table('uploaded_files', function (Blueprint $table) {
            $table->unsignedBigInteger('form_id')->nullable()->after('id'); // Adjust position as needed
            $table->foreign('form_id')->references('id')->on('jot_forms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uploaded_files', function (Blueprint $table) {
            $table->dropForeign(['form_id']);
            $table->dropColumn('form_id');
        });
    }
};
