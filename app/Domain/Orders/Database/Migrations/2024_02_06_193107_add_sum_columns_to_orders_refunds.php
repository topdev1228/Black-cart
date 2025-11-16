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
            $table->integer('tbyb_gross_sales_shop_amount')->default(0);
            $table->integer('tbyb_gross_sales_customer_amount')->default(0);
            $table->integer('tbyb_discounts_shop_amount')->default(0);
            $table->integer('tbyb_discounts_customer_amount')->default(0);
            $table->integer('upfront_gross_sales_shop_amount')->default(0);
            $table->integer('upfront_gross_sales_customer_amount')->default(0);
            $table->integer('upfront_discounts_shop_amount')->default(0);
            $table->integer('upfront_discounts_customer_amount')->default(0);
            $table->integer('order_refund_shop_amount')->default(0);
            $table->integer('order_refund_customer_amount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->dropColumn('tbyb_gross_sales_shop_amount');
        });

        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->dropColumn('tbyb_gross_sales_customer_amount');
        });

        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->dropColumn('upfront_gross_sales_shop_amount');
        });

        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->dropColumn('upfront_gross_sales_customer_amount');
        });

        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->dropColumn('tbyb_discounts_shop_amount');
        });

        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->dropColumn('tbyb_discounts_customer_amount');
        });

        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->dropColumn('upfront_discounts_shop_amount');
        });

        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->dropColumn('upfront_discounts_customer_amount');
        });

        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->dropColumn('order_refund_shop_amount');
        });

        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->dropColumn('order_refund_customer_amount');
        });
    }
};
