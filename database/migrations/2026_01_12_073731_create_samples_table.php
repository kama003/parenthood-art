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
        Schema::create('samples', function (Blueprint $table) {
            $table->id();
            $table->string('sample_id')->unique();
            $table->foreignId('donor_id')->constrained('donors')->onDelete('cascade');
            $table->foreignId('couple_id')->nullable()->constrained('couples')->onDelete('set null');
            $table->string('blood_group'); // Snapshot or inherited
            $table->integer('vials_count')->default(1);
            $table->date('freeze_date');
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['available', 'sold', 'returned', 'expired'])->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('samples');
    }
};
