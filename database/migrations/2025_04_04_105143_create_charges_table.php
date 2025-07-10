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
        Schema::create('charges', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 7, 1);
            $table->bigInteger('pocessid')->unsigned();
            $table->string('status',100)->default("pending");
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
        Schema::dropIfExists('charges');
    }
};
