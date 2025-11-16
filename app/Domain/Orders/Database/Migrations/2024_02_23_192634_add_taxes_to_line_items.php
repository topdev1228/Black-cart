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
        Schema::table('orders_line_items', function (Blueprint $table) {
            $table->integer('tax_shop_amount')->default(0);
            $table->integer('tax_customer_amount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders_line_items', function (Blueprint $table) {
            $table->dropColumn('tax_shop_amount');
            $table->dropColumn('tax_customer_amount');
        });
    }
};
