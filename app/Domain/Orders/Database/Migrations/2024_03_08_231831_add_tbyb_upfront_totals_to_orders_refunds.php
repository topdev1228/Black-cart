<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->integer('tbyb_total_shop_amount')->nullable();
            $table->integer('tbyb_total_customer_amount')->nullable();
            $table->integer('upfront_total_shop_amount')->nullable();
            $table->integer('upfront_total_customer_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->dropColumn('tbyb_total_shop_amount');
        });

        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->dropColumn('tbyb_total_customer_amount');
        });

        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->dropColumn('upfront_total_shop_amount');
        });

        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->dropColumn('upfront_total_customer_amount');
        });
    }
};
