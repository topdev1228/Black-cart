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
            $table->unique('source_id');
            $table->index('store_id');
        });

        Schema::table('orders_line_items', function (Blueprint $table) {
            $table->dropIndex('orders_line_items_source_id_index');
            $table->unique('source_id');
        });

        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->unique('source_refund_reference_id', 'orders_refunds_source_id_unique');
            $table->index('order_id');
        });

        Schema::table('orders_transactions', function (Blueprint $table) {
            $table->dropIndex('orders_transactions_source_id_index');
            $table->unique('source_id');
        });

        Schema::table('orders_trial_group', function (Blueprint $table) {
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_source_id_unique');
            $table->dropIndex('orders_store_id_index');
        });

        Schema::table('orders_line_items', function (Blueprint $table) {
            $table->index('source_id');
            $table->dropIndex('orders_line_items_source_id_unique');
        });

        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->dropIndex('orders_refunds_source_id_unique');
            $table->dropIndex('orders_refunds_order_id_index');
        });

        Schema::table('orders_transactions', function (Blueprint $table) {
            $table->index('source_id');
            $table->dropIndex('orders_transactions_source_id_unique');
        });

        Schema::table('orders_trial_group', function (Blueprint $table) {
            $table->index('source_id');
            $table->dropIndex('orders_trial_group_order_id_index');
        });
    }
};
