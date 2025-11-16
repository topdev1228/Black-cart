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
        Schema::table('orders_tbyb_net_sales', function (Blueprint $table) {
            $table->bigInteger('tbyb_gross_sales')->default(0)->change();
            $table->bigInteger('tbyb_discounts')->default(0)->change();
            $table->bigInteger('tbyb_refunded_gross_sales')->default(0)->change();
            $table->bigInteger('tbyb_refunded_discounts')->default(0)->change();
            $table->bigInteger('tbyb_net_sales')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders_tbyb_net_sales', function (Blueprint $table) {
            $table->integer('tbyb_gross_sales')->default(0)->change();
            $table->integer('tbyb_discounts')->default(0)->change();
            $table->integer('tbyb_refunded_gross_sales')->default(0)->change();
            $table->integer('tbyb_refunded_discounts')->default(0)->change();
            $table->integer('tbyb_net_sales')->default(0)->change();
        });
    }
};
