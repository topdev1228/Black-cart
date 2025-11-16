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
        Schema::create('orders_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('source_id')->index();
            $table->string('order_id')->index();
            $table->string('source_order_id')->index();
            $table->string('store_id')->index();

            $table->string('kind');
            $table->string('gateway');
            $table->string('payment_id');
            $table->string('parent_transaction_id')->nullable();
            $table->string('parent_transaction_source_id')->nullable();
            $table->string('customer_currency', 3);
            $table->string('shop_currency', 3);
            $table->integer('customer_amount');
            $table->integer('shop_amount');
            $table->integer('unsettled_customer_amount');
            $table->integer('unsettled_shop_amount');
            $table->string('status');
            $table->boolean('test')->default(false);
            $table->string('error_code')->nullable();
            $table->string('message')->nullable();
            $table->string('transaction_source_name')->nullable();
            $table->string('user_id')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('authorization_expires_at')->nullable();

            $table->json('receipt_json');
            $table->json('transaction_data');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_transactions');
    }
};
