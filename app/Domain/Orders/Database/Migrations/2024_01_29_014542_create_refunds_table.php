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
        Schema::create('orders_refunds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('source_refund_reference_id')->nullable();
            $table->string('source_refund_reference_gid')->nullable();
            $table->string('order_id');
            $table->integer('shop_amount');
            $table->string('shop_currency', 3);
            $table->integer('customer_amount');
            $table->string('customer_currency', 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_refunds');
    }
};
