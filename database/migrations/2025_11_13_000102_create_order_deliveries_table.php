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
        Schema::create('order_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('shipper_id')->nullable()->constrained('shippers')->nullOnDelete();
            $table->enum('status', ['pending', 'accepted', 'in_transit', 'completed', 'cancelled'])->default('pending');
            $table->enum('assignment_type', ['auto', 'manual', 'self-claim'])->default('self-claim');
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('tip_amount', 10, 2)->default(0);
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_deliveries');
    }
};
