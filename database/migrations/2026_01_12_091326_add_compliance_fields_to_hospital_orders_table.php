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
        Schema::table('hospital_orders', function (Blueprint $table) {
            $table->string('aadhaar_file_path')->nullable();
            $table->boolean('declaration_accepted')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('hospital_orders', function (Blueprint $table) {
            $table->dropColumn(['aadhaar_file_path', 'declaration_accepted']);
        });
    }
};
