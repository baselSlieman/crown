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
        Schema::table('gifts', function (Blueprint $table) {
            $table->string("type")->default('user');
            $table->dropForeign(['chat_id']);
            $table->unsignedBigInteger('chat_id')->nullable()->change();
            $table->foreign('chat_id')->references('id')->on('chats')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gifts', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropForeign(['chat_id']);
            $table->unsignedBigInteger('chat_id')->nullable(false)->change();
            $table->foreign('chat_id')->references('id')->on('chats')->onDelete('cascade');
        });
    }
};
