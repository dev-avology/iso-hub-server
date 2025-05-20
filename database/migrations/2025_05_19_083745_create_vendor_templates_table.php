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
        Schema::create('vendor_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('vendor_name')->nullable();
            $table->string('vendor_email')->nullable();
            $table->string('vendor_phone')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('login_url')->nullable();
            $table->string('rep_name')->nullable();
            $table->string('rep_email')->nullable();
            $table->string('rep_phone')->nullable();
            $table->text('notes')->nullable();
            $table->text('support_info')->nullable();
            $table->text('description')->nullable();
            $table->string('vendor_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_templates');
    }
};
