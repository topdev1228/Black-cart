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
        Schema::create('orders_returns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('source_id')->unique()->index();
            $table->string('order_id')->index();
            $table->string('store_id')->index();
            $table->string('source_order_id')->index();
            $table->string('shop_currency', 3);
            $table->string('customer_currency', 3);
            $table->string('name')->nullable();
            $table->string('status');
            $table->integer('total_quantity')->default(0);
            $table->integer('tbyb_gross_sales_shop_amount')->default(0);
            $table->integer('tbyb_gross_sales_customer_amount')->default(0);
            $table->integer('tbyb_discounts_shop_amount')->default(0);
            $table->integer('tbyb_discounts_customer_amount')->default(0);
            $table->integer('upfront_gross_sales_shop_amount')->default(0);
            $table->integer('upfront_gross_sales_customer_amount')->default(0);
            $table->integer('upfront_discounts_shop_amount')->default(0);
            $table->integer('upfront_discounts_customer_amount')->default(0);
            $table->integer('tbyb_tax_shop_amount')->default(0);
            $table->integer('tbyb_tax_customer_amount')->default(0);
            $table->integer('upfront_tax_shop_amount')->default(0);
            $table->integer('upfront_tax_customer_amount')->default(0);
            $table->integer('tbyb_total_shop_amount')->default(0);
            $table->integer('tbyb_total_customer_amount')->default(0);
            $table->json('return_data');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('orders_returns_line_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('source_id')->unique()->index();
            $table->string('order_return_id')->index();
            $table->string('source_return_id')->index();
            $table->string('line_item_id')->index();
            $table->string('customer_note')->nullable();
            $table->integer('quantity')->default(0);
            $table->string('return_reason')->nullable();
            $table->string('return_reason_note')->nullable();
            $table->string('shop_currency', 3);
            $table->string('customer_currency', 3);
            $table->integer('gross_sales_shop_amount');
            $table->integer('gross_sales_customer_amount');
            $table->integer('discounts_shop_amount');
            $table->integer('discounts_customer_amount');
            $table->integer('tax_customer_amount');
            $table->integer('tax_shop_amount');
            $table->json('return_line_item_data');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_returns');
        Schema::dropIfExists('orders_returns_line_items');
    }
};
