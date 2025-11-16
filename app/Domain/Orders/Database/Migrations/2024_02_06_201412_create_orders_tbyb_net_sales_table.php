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
        Schema::create('orders_tbyb_net_sales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('store_id')->index();
            $table->date('date_start');
            $table->date('date_end');
            $table->timestamp('time_range_start');
            $table->timestamp('time_range_end');
            $table->string('currency', 3);
            $table->integer('tbyb_gross_sales')->default(0);
            $table->integer('tbyb_discounts')->default(0);
            $table->integer('tbyb_refunded_gross_sales')->default(0);
            $table->integer('tbyb_refunded_discounts')->default(0);
            $table->integer('tbyb_net_sales')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_tbyb_net_sales');
    }
};
