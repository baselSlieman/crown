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
        Schema::create('ich_transactions', function (Blueprint $table) {
            $table->id();
            $table->decimal("amount",7,1);
            $table->string('type');
            $table->string('status')->default("requested");
            $table->softDeletes();
            $table->timestamps();
            $table->foreignIdFor(\App\Models\Chat::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\Ichancy::class)->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ich_transactions');
    }
};
