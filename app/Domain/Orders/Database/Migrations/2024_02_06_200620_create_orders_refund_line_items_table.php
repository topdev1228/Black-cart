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
        Schema::create('orders_refund_line_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('refund_id');
            $table->string('line_item_id');
            $table->integer('quantity');
            $table->string('shop_currency', 3);
            $table->string('customer_currency', 3);
            $table->integer('shop_subtotal');
            $table->integer('shop_tax');
            $table->integer('customer_subtotal');
            $table->integer('customer_tax');
            $table->integer('gross_sales_shop_amount');
            $table->integer('gross_sales_customer_amount');
            $table->integer('discounts_shop_amount');
            $table->integer('discounts_customer_amount');
            $table->boolean('is_tbyb')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_refund_line_items');
    }
};
