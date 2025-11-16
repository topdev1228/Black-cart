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
        Schema::create('payments_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('source_id')->nullable()->default(null);
            $table->string('source_transaction_name');
            $table->string('order_id')->index();
            $table->string('store_id')->index();
            $table->string('source_order_id')->index();
            $table->datetime('auth_expires_at')->nullable()->default(null);
            $table->integer('shop_amount');
            $table->string('shop_currency', 3);
            $table->integer('customer_amount');
            $table->string('customer_currency', 3);
            $table->string('status');
            $table->string('kind');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments_transactions');
    }
};
