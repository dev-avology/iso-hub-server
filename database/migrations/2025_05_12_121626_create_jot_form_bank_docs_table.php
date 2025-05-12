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
       Schema::create('jot_form_bank_docs', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('jot_form_id')->constrained('jot_forms')->onDelete('cascade'); // Foreign key referencing jot_forms table
            $table->string('name')->nullable(); // Nullable name field
            $table->string('path')->nullable(); // Nullable path field
            $table->timestamps(); // Timestamps for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jot_form_bank_docs');
    }
};
