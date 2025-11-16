<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders_transactions', function (Blueprint $table) {
            $table->string('order_name')->after('source_order_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders_transactions', function (Blueprint $table) {
            $table->dropColumn('order_name');
        });
    }
};
