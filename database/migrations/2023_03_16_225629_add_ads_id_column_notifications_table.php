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
        Schema::table('notification', function (Blueprint $table) {
            $table->bigInteger('ads_id')->after('rent_item_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification', function (Blueprint $table) {
            Schema::dropIfExists('ads_id');
        });
    }
};
