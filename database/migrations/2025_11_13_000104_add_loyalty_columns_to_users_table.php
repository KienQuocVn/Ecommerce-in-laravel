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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'loyalty_points')) {
                $table->unsignedBigInteger('loyalty_points')->default(0)->after('status');
            }
            if (!Schema::hasColumn('users', 'loyalty_tier')) {
                $table->string('loyalty_tier', 20)->default('bronze')->after('loyalty_points');
            }
            if (!Schema::hasColumn('users', 'total_spent')) {
                $table->decimal('total_spent', 12, 2)->default(0)->after('loyalty_tier');
            }
            if (!Schema::hasColumn('users', 'total_orders')) {
                $table->unsignedInteger('total_orders')->default(0)->after('total_spent');
            }
            if (!Schema::hasColumn('users', 'last_order_at')) {
                $table->timestamp('last_order_at')->nullable()->after('total_orders');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'last_order_at')) {
                $table->dropColumn('last_order_at');
            }
            if (Schema::hasColumn('users', 'total_orders')) {
                $table->dropColumn('total_orders');
            }
            if (Schema::hasColumn('users', 'total_spent')) {
                $table->dropColumn('total_spent');
            }
            if (Schema::hasColumn('users', 'loyalty_tier')) {
                $table->dropColumn('loyalty_tier');
            }
            if (Schema::hasColumn('users', 'loyalty_points')) {
                $table->dropColumn('loyalty_points');
            }
        });
    }
};
