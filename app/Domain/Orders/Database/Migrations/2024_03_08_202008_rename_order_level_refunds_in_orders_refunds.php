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
            $table->renameColumn('order_refund_shop_amount', 'order_level_refund_shop_amount');
        });
        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->renameColumn('order_refund_customer_amount', 'order_level_refund_customer_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->renameColumn('order_level_refund_shop_amount', 'order_refund_shop_amount');
        });
        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->renameColumn('order_level_refund_customer_amount', 'order_refund_customer_amount');
        });
    }
};
