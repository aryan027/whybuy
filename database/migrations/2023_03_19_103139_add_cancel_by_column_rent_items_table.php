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
            $table->bigInteger('cancel_by')->after('status')->nullable();
            $table->text('cancel_reason')->after('cancel_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rent_items', function (Blueprint $table) {
            Schema::dropIfExists('cancel_by');
            Schema::dropIfExists('cancel_reason');
        });
    }
};
