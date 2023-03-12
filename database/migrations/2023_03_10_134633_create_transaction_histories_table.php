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
        Schema::create('transaction_histories', function (Blueprint $table) {
            $table->id();
            $table->string('txn_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('ad_id')->nullable();
            $table->foreign('ad_id')->references('id')->on('advertisements')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('type')->default(0)->comment('0 - debit, 1 - credit, 2 - hold');
            $table->decimal('amount',10,2);
            $table->string('remark')->nullable();
            $table->json('payload')->nullable();
            $table->enum('payment_method',['debit','credit','upi','net_banking'])->nullable();
            $table->enum('txn_status',['success','pending','failed'])->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_histories');
    }
};
