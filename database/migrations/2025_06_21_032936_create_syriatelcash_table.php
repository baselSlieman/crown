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
        Schema::create('syriatelcashes', function (Blueprint $table) {
            $table->id(); // عمود id مفتاح رئيسي
            $table->string('phone', 12)->nullable(); // عمود phone رقم 12 خانة يمكن أن يكون null
            $table->string('code', 12)->nullable(); // عمود code رقم 12 خانة يمكن أن يكون null
            $table->boolean('status')->default(true);
            $table->integer('codeOrder')->default(1); // عمود status نوع boolean والقيمة الافتراضية true
            $table->integer('type')->default(1); // عمود info نص 255 خانة يمكن أن يكون null
            $table->string('username'); // عمود username نص
            $table->string('userid', 12)->nullable(); // عمود userid رقم 12 خانة يمكن أن يكون null
            $table->text('userHistory')->nullable(); // عمود userHistory نصي 300 خانة يمكن أن يكون null
            $table->text('refreshBalance')->nullable(); // عمود refreshBalance نصي 300 خانة يمكن أن يكون null
            $table->text('MaarchentHistory')->nullable(); // عمود MaarchentHistory نصي 300 خانة يمكن أن يكون null
            $table->string('info', 255)->nullable();
            $table->text('more')->nullable(); // عمود more نص
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('syriatelcashes');
    }
};
