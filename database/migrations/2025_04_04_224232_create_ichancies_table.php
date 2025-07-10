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
        Schema::create('ichancies', function (Blueprint $table) {
            $table->id();
            $table->string('e_username',255);
            $table->string('e_password',20);
            $table->string('username',255)->nullable();
            $table->string('password',20)->nullable();
            $table->string('status',100)->default('requested');
            $table->string('identifier')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreignIdFor(\App\Models\Chat::class)->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ichancies');
    }
};
