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
        Schema::table('rent_items', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_id')->after('user_id');
            $table->foreign('owner_id')->on('users')->references('id')->onUpdate('cascade')->onDelete('cascade');
            $table->float('deposite_amount', 8, 2)->after('price'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rent_items', function (Blueprint $table) {
            $table->dropColumn('deposite_amount');
        });
    }
};
