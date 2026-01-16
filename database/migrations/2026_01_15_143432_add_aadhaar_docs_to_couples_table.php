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
        Schema::table('couples', function (Blueprint $table) {
            $table->string('partner_1_aadhaar_path')->nullable()->after('partner_1_name');
            $table->string('partner_2_aadhaar_path')->nullable()->after('partner_2_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('couples', function (Blueprint $table) {
            $table->dropColumn(['partner_1_aadhaar_path', 'partner_2_aadhaar_path']);
        });
    }
};
