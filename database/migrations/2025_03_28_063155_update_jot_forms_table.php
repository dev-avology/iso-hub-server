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
            $table->string('dba')->nullable()->after('signature');
            $table->text('address2')->nullable()->after('dba');
            $table->string('state')->nullable()->after('address2');
            $table->string('city')->nullable()->after('state');
            $table->string('pincode')->nullable()->after('city');
            $table->enum('is_same_shipping_address', [0, 1])->default(0)->nullable()->after('pincode');
            $table->integer('status')->default(0)->nullable()->after('is_same_shipping_address')->comment('status: new (0) | in_review (1) | approved (2) | declined (3)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jot_forms', function (Blueprint $table) {
            //
        });
    }
};
