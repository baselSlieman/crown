<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id(); // العمود id كمفتاح رئيسي
            $table->integer('bonus')->default(1);
             $table->boolean('bonusStatus')->default(true);
            $table->integer('affilliate')->default(1);
            $table->boolean('affilliateStatus')->default(true);
            $table->integer('cashback')->default(1);
            $table->boolean('cashbackStatus')->default(true);
            $table->boolean('bootStatus')->default(true);
            $table->integer('extra_col')->default(1);
            $table->string('description')->nullable(); // وصف كسترينغ
            $table->timestamps(); // لأعمدة created_at و updated_at – اختياري
        });

        DB::table('settings')->insert([
        'bonus' => 1,
        'affilliate' => 1,
        'cashback' => 1,
        'extra_col' => 1,
        'bonusStatus' => true,
        'affilliateStatus' => true,
        'cashbackStatus' => true,
        'bootStatus' => true,
        'description' => 'default_setting'
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }

};
