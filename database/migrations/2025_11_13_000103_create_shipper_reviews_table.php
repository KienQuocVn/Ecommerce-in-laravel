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
        Schema::create('shipper_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('order_deliveries')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shipper_id')->constrained('shippers')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->boolean('is_liked')->default(false);
            $table->decimal('tip_amount', 10, 2)->default(0);
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['delivery_id', 'customer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipper_reviews');
    }
};
