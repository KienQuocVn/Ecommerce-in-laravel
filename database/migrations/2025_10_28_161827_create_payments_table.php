<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('order_id');
            $table->string('provider', 50); // stripe, paypal, momo, vnpay, stripe
            $table->string('status', 50)->default('pending'); // pending, succeeded, failed, canceled
            $table->decimal('amount', 15, 2);
            $table->string('currency', 10)->default('VND');
            $table->string('transaction_id')->nullable();
            $table->string('gateway_event_id')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('order_id')
                  ->references('id')
                  ->on('orders')
                  ->onDelete('cascade');
            
            // Indexes
            $table->index('order_id');
            $table->index('transaction_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};