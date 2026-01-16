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
        Schema::create('donors', function (Blueprint $table) {
            $table->id();
            $table->string('donor_number')->unique();
            $table->integer('age');
            $table->string('aadhaar_number', 12)->nullable(); // 12 digits
            $table->string('blood_group');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('eye_color')->nullable();
            $table->string('hair_color')->nullable();
            $table->enum('body_structure', ['Slim', 'Moderate', 'Obese'])->nullable();
            $table->string('complexion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donors');
    }
};
