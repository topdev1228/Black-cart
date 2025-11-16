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
            $table->string('deposit_type')->nullable()->after('selling_plan_id');
            $table->integer('deposit_value')->nullable()->after('deposit_type');
            $table->bigInteger('deposit_shop_amount')->nullable()->after('deposit_value');
            $table->bigInteger('deposit_customer_amount')->nullable()->after('deposit_shop_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders_line_items', function (Blueprint $table) {
            $table->dropColumn('deposit_type');
            $table->dropColumn('deposit_value');
            $table->dropColumn('deposit_shop_amount');
            $table->dropColumn('deposit_customer_amount');
        });
    }
};
