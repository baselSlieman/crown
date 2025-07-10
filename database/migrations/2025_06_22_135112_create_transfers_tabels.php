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
       Schema::create('transfers', function (Blueprint $table) {
            $table->id(); // معرف الحوالة
            $table->string('sender_number'); // رقم المرسل
            $table->string('receiver_number'); // رقم المستفيد
            $table->decimal('amount', 15, 2); // قيمة المبلغ (مثلاً مع رقمين عشريين)
            $table->timestamps(); // created_at و updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('transfers');
    }
};
