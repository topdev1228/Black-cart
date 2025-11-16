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
            $table->integer('deposit_shop_amount');
            $table->integer('deposit_customer_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders_refund_line_items', function (Blueprint $table) {
            $table->dropColumn('deposit_shop_amount');
        });

        Schema::table('orders_refund_line_items', function (Blueprint $table) {
            $table->dropColumn('deposit_customer_amount');
        });
    }
};
