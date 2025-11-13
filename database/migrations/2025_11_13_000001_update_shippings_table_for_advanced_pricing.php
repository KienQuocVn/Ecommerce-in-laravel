<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shippings', function (Blueprint $table) {
            if (!Schema::hasColumn('shippings', 'code')) {
                $table->string('code')->nullable()->after('id');
            }

            if (!Schema::hasColumn('shippings', 'service_level')) {
                $table->string('service_level')->nullable()->after('type');
            }

            if (!Schema::hasColumn('shippings', 'delivery_zone')) {
                $table->string('delivery_zone')->nullable()->after('service_level');
            }

            if (!Schema::hasColumn('shippings', 'pricing_strategy')) {
                $table->string('pricing_strategy', 20)->default('flat')->after('price');
            }

            if (!Schema::hasColumn('shippings', 'percentage_rate')) {
                $table->decimal('percentage_rate', 5, 2)->default(0)->after('pricing_strategy');
            }

            if (!Schema::hasColumn('shippings', 'min_cart_total')) {
                $table->decimal('min_cart_total', 10, 2)->nullable()->after('percentage_rate');
            }

            if (!Schema::hasColumn('shippings', 'max_cart_total')) {
                $table->decimal('max_cart_total', 10, 2)->nullable()->after('min_cart_total');
            }

            if (!Schema::hasColumn('shippings', 'estimated_time')) {
                $table->string('estimated_time')->nullable()->after('max_cart_total');
            }

            if (!Schema::hasColumn('shippings', 'supports_cod')) {
                $table->boolean('supports_cod')->default(false)->after('estimated_time');
            }

            if (!Schema::hasColumn('shippings', 'is_recommended')) {
                $table->boolean('is_recommended')->default(false)->after('supports_cod');
            }

            if (!Schema::hasColumn('shippings', 'description')) {
                $table->text('description')->nullable()->after('is_recommended');
            }

            if (!Schema::hasColumn('shippings', 'priority')) {
                $table->unsignedTinyInteger('priority')->default(10)->after('description');
            }
        });

        $shippings = DB::table('shippings')->select('id', 'code', 'type')->get();
        foreach ($shippings as $shipping) {
            if (!$shipping->code) {
                $generatedCode = (string) Str::of($shipping->type ?? ('shipping-' . $shipping->id))
                    ->upper()
                    ->ascii()
                    ->slug('-')
                    ->replace('-', '_')
                    ->limit(40, '')
                    ->prepend('SHIP_');

                DB::table('shippings')
                    ->where('id', $shipping->id)
                    ->update([
                        'code' => $generatedCode,
                        'service_level' => $shipping->type,
                        'pricing_strategy' => 'flat',
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shippings', function (Blueprint $table) {
            if (Schema::hasColumn('shippings', 'priority')) {
                $table->dropColumn('priority');
            }
            if (Schema::hasColumn('shippings', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('shippings', 'is_recommended')) {
                $table->dropColumn('is_recommended');
            }
            if (Schema::hasColumn('shippings', 'supports_cod')) {
                $table->dropColumn('supports_cod');
            }
            if (Schema::hasColumn('shippings', 'estimated_time')) {
                $table->dropColumn('estimated_time');
            }
            if (Schema::hasColumn('shippings', 'max_cart_total')) {
                $table->dropColumn('max_cart_total');
            }
            if (Schema::hasColumn('shippings', 'min_cart_total')) {
                $table->dropColumn('min_cart_total');
            }
            if (Schema::hasColumn('shippings', 'percentage_rate')) {
                $table->dropColumn('percentage_rate');
            }
            if (Schema::hasColumn('shippings', 'pricing_strategy')) {
                $table->dropColumn('pricing_strategy');
            }
            if (Schema::hasColumn('shippings', 'delivery_zone')) {
                $table->dropColumn('delivery_zone');
            }
            if (Schema::hasColumn('shippings', 'service_level')) {
                $table->dropColumn('service_level');
            }
            if (Schema::hasColumn('shippings', 'code')) {
                $table->dropColumn('code');
            }
        });
    }
};
