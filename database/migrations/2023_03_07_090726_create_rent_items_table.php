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
        Schema::create('rent_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ads_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('ads_id')->on('advertisements')->references('id')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->on('users')->references('id')->onUpdate('cascade')->onDelete('cascade');
            $table->string('rent_type')->comment('hour,day,week,month,year');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->string('price');
            $table->longText('description')->nullable();
            $table->string('purpose')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0-pending,1-approved,2-cancel');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rent_items');
    }
};
