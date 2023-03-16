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
            $table->unsignedBigInteger('rent_item_id');
            $table->foreign('rent_item_id')->on('rent_items')->references('id')->onUpdate('cascade')->onDelete('cascade');
            $table->tinyInteger('is_accept')->default(0)->comment("0=Not aceepted, 1=Accepted");
            $table->Integer('owner_id')->nullable();
            $table->tinyInteger('is_confirm')->default(0)->comment("0=Not confirmed, 1=confirmed");
            $table->Integer('user_id')->nullable();
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
