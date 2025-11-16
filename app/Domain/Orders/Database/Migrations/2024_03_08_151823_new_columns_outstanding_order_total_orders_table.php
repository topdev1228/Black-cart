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
            $table->renameColumn('total_order_refunds_shop_amount', 'total_order_level_refunds_shop_amount');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('total_order_refunds_customer_amount', 'total_order_level_refunds_customer_amount');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('original_outstanding_shop_amount')->change();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('original_outstanding_customer_amount')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('total_order_level_refunds_shop_amount', 'total_order_refunds_shop_amount');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('total_order_level_refunds_customer_amount', 'total_order_refunds_customer_amount');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->string('original_outstanding_shop_amount')->change();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->string('original_outstanding_customer_amount')->change();
        });
    }
};
