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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('quantity')->unsigned()->nullable();
            $table->decimal('price', 5, 2);
            $table->boolean('available')->default(true);
            $table->string('image')->nullable()->default('defProdimage.jpg');
            $table->softDeletes();
            $table->timestamps();
            $table->foreignIdFor(\App\Models\Category::class)->nullable()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
