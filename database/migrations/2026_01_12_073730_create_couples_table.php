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
        Schema::create('couples', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique();
            $table->string('partner_1_name');
            $table->string('partner_2_name')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->enum('status', ['active', 'inactive', 'discharged'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('couples');
    }
};
