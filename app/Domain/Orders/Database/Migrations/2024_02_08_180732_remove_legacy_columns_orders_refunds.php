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
        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->dropColumn('source_refund_reference_gid');
        });

        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->dropColumn('shop_amount');
        });

        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->dropColumn('customer_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders_refunds', function (Blueprint $table) {
            $table->integer('shop_amount');
            $table->integer('customer_amount');
            $table->string('source_refund_reference_gid')->nullable();
        });
    }
};
