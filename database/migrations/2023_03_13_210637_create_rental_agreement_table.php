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
        Schema::create('rental_agreement', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->on('users')->references('id')->onUpdate('cascade')->onDelete('cascade');
            $table->tinyInteger('user_type')->default(1)->comment('1=user,2=owner');
            $table->unsignedBigInteger('rent_item_id');
            $table->foreign('rent_item_id')->on('rent_items')->references('id')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->string('phone');
            $table->text('address');
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('purpose')->nullable();
            $table->text('shelter')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_agreement');
    }
};
