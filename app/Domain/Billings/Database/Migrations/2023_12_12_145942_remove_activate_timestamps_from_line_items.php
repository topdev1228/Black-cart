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
            $table->dropColumn('activated_at');
        });
        Schema::table('billings_subscription_line_items', function (Blueprint $table) {
            $table->dropColumn('deactivated_at');
        });

        Schema::table('billings_app_usage_configs', function (Blueprint $table) {
            $table->dropColumn('activated_at');
        });
        Schema::table('billings_app_usage_configs', function (Blueprint $table) {
            $table->dropColumn('deactivated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billings_subscription_line_items', function (Blueprint $table) {
            $table->dateTime('activated_at')->nullable();
            $table->dateTime('deactivated_at')->nullable();
        });

        Schema::table('billings_app_usage_configs', function (Blueprint $table) {
            $table->dateTime('activated_at')->nullable();
            $table->dateTime('deactivated_at')->nullable();
        });
    }
};
