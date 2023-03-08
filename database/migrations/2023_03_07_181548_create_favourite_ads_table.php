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
        Schema::create('favourite_ads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ads_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('ads_id')->on('advertisements')->references('id')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->on('users')->references('id')->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favourite_ads');
    }
};
