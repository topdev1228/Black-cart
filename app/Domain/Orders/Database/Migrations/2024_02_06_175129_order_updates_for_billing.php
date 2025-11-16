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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->boolean('taxes_included')->default(false);
            $table->boolean('taxes_exempt')->default(false);
            $table->string('tags')->default('');
            $table->string('discount_codes')->default('');
            $table->boolean('test')->default(false);
            $table->string('payment_terms_id')->nullable();
            $table->string('payment_terms_name')->nullable();
            $table->string('payment_terms_type')->nullable();
            $table->string('shop_currency', 3)->default('USD');
            $table->string('customer_currency', 3)->default('USD');
            $table->integer('total_shop_amount')->default(0);
            $table->integer('total_customer_amount')->default(0);
            $table->integer('outstanding_shop_amount')->default(0);
            $table->integer('outstanding_customer_amount')->default(0);
            $table->integer('original_tbyb_gross_sales_shop_amount')->default(0);
            $table->integer('original_tbyb_gross_sales_customer_amount')->default(0);
            $table->integer('original_upfront_gross_sales_shop_amount')->default(0);
            $table->integer('original_upfront_gross_sales_customer_amount')->default(0);
            $table->integer('original_total_gross_sales_shop_amount')->default(0);
            $table->integer('original_total_gross_sales_customer_amount')->default(0);
            $table->integer('original_tbyb_discounts_shop_amount')->default(0);
            $table->integer('original_tbyb_discounts_customer_amount')->default(0);
            $table->integer('original_upfront_discounts_shop_amount')->default(0);
            $table->integer('original_upfront_discounts_customer_amount')->default(0);
            $table->integer('original_total_discounts_shop_amount')->default(0);
            $table->integer('original_total_discounts_customer_amount')->default(0);
            $table->integer('tbyb_refund_gross_sales_shop_amount')->default(0);
            $table->integer('tbyb_refund_gross_sales_customer_amount')->default(0);
            $table->integer('upfront_refund_gross_sales_shop_amount')->default(0);
            $table->integer('upfront_refund_gross_sales_customer_amount')->default(0);
            $table->integer('total_order_refunds_shop_amount')->default(0);
            $table->integer('total_order_refunds_customer_amount')->default(0);
            $table->integer('tbyb_refund_discounts_shop_amount')->default(0);
            $table->integer('tbyb_refund_discounts_customer_amount')->default(0);
            $table->integer('upfront_refund_discounts_shop_amount')->default(0);
            $table->integer('upfront_refund_discounts_customer_amount')->default(0);
            $table->integer('tbyb_net_sales_shop_amount')->default(0);
            $table->integer('tbyb_net_sales_customer_amount')->default(0);
            $table->integer('upfront_net_sales_shop_amount')->default(0);
            $table->integer('upfront_net_sales_customer_amount')->default(0);
            $table->integer('total_net_sales_shop_amount')->default(0);
            $table->integer('total_net_sales_customer_amount')->default(0);
        });

        Schema::table('orders_line_items', function (Blueprint $table) {
            $table->boolean('is_tbyb')->default(false);
            $table->string('selling_plan_id')->nullable();
            $table->string('shop_currency')->default('USD');
            $table->string('customer_currency')->default('USD');
            $table->integer('price_shop_amount')->default(0);
            $table->integer('price_customer_amount')->default(0);
            $table->integer('total_price_shop_amount')->default(0);
            $table->integer('total_price_customer_amount')->default(0);
            $table->integer('discount_shop_amount')->default(0);
            $table->integer('discount_customer_amount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['name',
                'taxes_included',
                'taxes_exempt',
                'tags',
                'discount_codes',
                'test',
                'payment_terms_id',
                'payment_terms_name',
                'payment_terms_type',
                'shop_currency',
                'customer_currency',
                'total_shop_amount',
                'total_customer_amount',
                'outstanding_shop_amount',
                'outstanding_customer_amount',
                'original_tbyb_gross_sales_shop_amount',
                'original_tbyb_gross_sales_customer_amount',
                'original_upfront_gross_sales_shop_amount',
                'original_upfront_gross_sales_customer_amount',
                'original_total_gross_sales_shop_amount',
                'original_total_gross_sales_customer_amount',
                'original_tbyb_discounts_shop_amount',
                'original_tbyb_discounts_customer_amount',
                'original_upfront_discounts_shop_amount',
                'original_upfront_discounts_customer_amount',
                'original_total_discounts_shop_amount',
                'original_total_discounts_customer_amount',
                'tbyb_refund_gross_sales_shop_amount',
                'tbyb_refund_gross_sales_customer_amount',
                'upfront_refund_gross_sales_shop_amount',
                'upfront_refund_gross_sales_customer_amount',
                'total_order_refunds_shop_amount',
                'total_order_refunds_customer_amount',
                'tbyb_refund_discounts_shop_amount',
                'tbyb_refund_discounts_customer_amount',
                'upfront_refund_discounts_shop_amount',
                'upfront_refund_discounts_customer_amount',
                'tbyb_net_sales_shop_amount',
                'tbyb_net_sales_customer_amount',
                'upfront_net_sales_shop_amount',
                'upfront_net_sales_customer_amount',
                'total_net_sales_shop_amount',
                'total_net_sales_customer_amount',
            ]);
        });

        Schema::table('orders_line_items', function (Blueprint $table) {
            $table->dropColumn([
                'is_tbyb',
                'selling_plan_id',
                'shop_currency',
                'customer_currency',
                'price_shop_amount',
                'price_customer_amount',
                'total_price_shop_amount',
                'total_price_customer_amount',
                'discount_shop_amount',
                'discount_customer_amount',
            ]);
        });
    }
};
