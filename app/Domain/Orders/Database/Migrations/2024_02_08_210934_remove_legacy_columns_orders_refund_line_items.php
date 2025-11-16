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
        Schema::table('orders_refund_line_items', function (Blueprint $table) {
            $table->dropColumn('shop_subtotal');
        });

        Schema::table('orders_refund_line_items', function (Blueprint $table) {
            $table->dropColumn('shop_tax');
        });

        Schema::table('orders_refund_line_items', function (Blueprint $table) {
            $table->dropColumn('customer_subtotal');
        });

        Schema::table('orders_refund_line_items', function (Blueprint $table) {
            $table->dropColumn('customer_tax');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders_refund_line_items', function (Blueprint $table) {
            $table->integer('shop_subtotal');
            $table->integer('shop_tax');
            $table->integer('customer_subtotal');
            $table->integer('customer_tax');
        });
    }
};
