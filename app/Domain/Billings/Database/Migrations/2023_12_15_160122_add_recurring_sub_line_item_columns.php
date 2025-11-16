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
            $table->renameColumn('capped_amount', 'usage_capped_amount');
        });
        Schema::table('billings_subscription_line_items', function (Blueprint $table) {
            $table->integer('usage_capped_amount')->nullable()->change();
        });

        Schema::table('billings_subscription_line_items', function (Blueprint $table) {
            $table->renameColumn('capped_amount_currency', 'usage_capped_amount_currency');
        });
        Schema::table('billings_subscription_line_items', function (Blueprint $table) {
            $table->string('usage_capped_amount_currency', 3)->default('USD')
                ->comment('ISO 4217')->change();
        });

        Schema::table('billings_subscription_line_items', function (Blueprint $table) {
            $table->integer('recurring_amount')->after('terms')->nullable();
            $table->string('recurring_amount_currency', 3)->after('recurring_amount')
                ->default('USD')->comment('ISO 4217');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billings_subscription_line_items', function (Blueprint $table) {
            $table->renameColumn('usage_capped_amount', 'capped_amount');
        });
        Schema::table('billings_subscription_line_items', function (Blueprint $table) {
            $table->integer('capped_amount')->change();
        });

        Schema::table('billings_subscription_line_items', function (Blueprint $table) {
            $table->renameColumn('usage_capped_amount_currency', 'capped_amount_currency');
        });
        Schema::table('billings_subscription_line_items', function (Blueprint $table) {
            $table->string('capped_amount_currency')->comment('ISO 4217')->change();
        });

        Schema::table('billings_subscription_line_items', function (Blueprint $table) {
            $table->dropColumn('recurring_amount');
        });
        Schema::table('billings_subscription_line_items', function (Blueprint $table) {
            $table->dropColumn('recurring_amount_currency');
        });
    }
};
