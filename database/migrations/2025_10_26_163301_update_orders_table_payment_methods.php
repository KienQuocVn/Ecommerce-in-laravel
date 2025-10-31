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
        Schema::table('orders', function (Blueprint $table) {
            // Update payment_method enum to include momo and vnpay
            $table->enum('payment_method', ['cod', 'paypal', 'momo', 'vnpay', 'stripe'])->default('cod')->change();

            // Update payment_status enum to include pending
            $table->enum('payment_status', ['paid', 'unpaid', 'pending'])->default('unpaid')->change();

            // Update status enum to include process
            $table->enum('status', ['new', 'progress', 'process', 'delivered', 'cancel'])->default('new')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Revert payment_method enum
            $table->enum('payment_method', ['cod', 'paypal', 'momo', 'vnpay', 'stripe'])->default('cod')->change();

            // Revert payment_status enum
            $table->enum('payment_status', ['paid', 'unpaid', 'pending'])->default('unpaid')->change();

            // Revert status enum
            $table->enum('status', ['new', 'progress', 'process', 'delivered', 'cancel'])->default('new')->change();
        });
    }
};
