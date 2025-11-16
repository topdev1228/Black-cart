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
            $table->boolean('is_first_of_billing_period')->default(false)->after('tbyb_net_sales');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders_tbyb_net_sales', function (Blueprint $table) {
            $table->dropColumn('is_first_of_billing_period');
        });
    }
};
