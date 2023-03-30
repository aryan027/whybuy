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
        Schema::table('advertisements', function (Blueprint $table) {
            $table->unsignedBigInteger('address_id')->after('owner_type');
            $table->foreign('address_id')->on('addresses')->references('id')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('rent_items', function (Blueprint $table) {
            $table->unsignedBigInteger('address_id')->after('deposite_amount');
            $table->foreign('address_id')->on('addresses')->references('id')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
