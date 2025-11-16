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
            $table->integer('tax_shop_amount');
            $table->integer('tax_customer_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders_refund_line_items', function (Blueprint $table) {
            $table->dropColumn('tax_shop_amount');
            $table->dropColumn('tax_customer_amount');
        });
    }
};
