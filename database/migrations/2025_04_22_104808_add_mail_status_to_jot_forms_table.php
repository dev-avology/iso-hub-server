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
            $table->integer('mail_status')
                ->nullable()
                ->default(0)
                ->comment('0 = Pending, 1 = Mail Sent, 2 = Image Uploaded')
                ->after('clear_signature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jot_forms', function (Blueprint $table) {
            $table->dropColumn('mail_status');
        });
    }
};
