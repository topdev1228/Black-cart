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
        Schema::table('billings_subscriptions', function (Blueprint $table) {
            $table->datetime('current_period_end')->nullable()->after('status');
            $table->integer('trial_days')->default(0)->after('current_period_end');
            $table->datetime('trial_period_end')->nullable()->after('trial_days');
            $table->boolean('is_test')->default(false)->after('trial_period_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billings_subscriptions', function (Blueprint $table) {
            $table->dropColumn('is_test');
            $table->dropColumn('trial_period_end');
            $table->dropColumn('trial_days');
            $table->dropColumn('current_period_end');
        });
    }
};
