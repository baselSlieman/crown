<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->string("client");
            $table->decimal("amount",7,1);
            $table->decimal("affiliate_amount",7,1);
            $table->string("status")->default("pending");
            $table->softDeletes();
            $table->timestamps();
            $table->string('month_at',7);
            $table->foreignIdFor(\App\Models\Chat::class)->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliates');
    }
};
