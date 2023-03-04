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
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('ad_id');
            $table->unsignedBigInteger('category');
            $table->foreign('category')->references('id')->on('categories')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('sub_category');
            $table->foreign('sub_category')->references('id')->on('sub_categories')->onUpdate('cascade')->onDelete('cascade');
            $table->string('title');
            $table->longText('description')->nullable();
            $table->string('brand')->nullable();
            $table->string('currency')->nullable();
            $table->string('deposit_amount')->nullable();
            $table->string('hourly_rent')->nullable();
            $table->string('daily_rent')->nullable();
            $table->string('weekly_rent')->nullable();
            $table->string('monthly_rent')->nullable();
            $table->string('yearly_rent')->nullable();
            $table->integer('rent_base')->nullable();
            $table->string('item_condition')->nullable();
            $table->string('owner_type')->nullable();
            $table->string('address')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->boolean('status')->default(false);
            $table->boolean('published')->default(false);
            $table->boolean('approved')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
