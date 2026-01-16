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
        Schema::create('hospital_orders', function (Blueprint $table) {
            $table->id();
            $table->string('hospital_name');
            $table->foreignId('couple_id')->constrained('couples')->onDelete('cascade');
            $table->foreignId('sample_id')->constrained('samples')->onDelete('cascade');
            $table->integer('vials_count');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'dispatched', 'delivered', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospital_orders');
    }
};
