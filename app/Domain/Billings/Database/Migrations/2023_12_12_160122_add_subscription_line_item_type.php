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
        Schema::table('billings_subscription_line_items', function (Blueprint $table) {
            $table->string('type')->default('usage')
                ->comment('usage|recurring')->after('shopify_app_subscription_line_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billings_subscription_line_items', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
