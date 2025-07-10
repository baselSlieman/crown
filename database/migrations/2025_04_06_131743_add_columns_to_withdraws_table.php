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
        Schema::table('withdraws', function (Blueprint $table) {
            $table->decimal('finalAmount',7,1)->default(0);
            $table->decimal('discountAmount',7,1)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdraws', function (Blueprint $table) {
            $table->dropColumn('finalAmount');
            $table->dropColumn('discountAmount');
        });
    }
};
