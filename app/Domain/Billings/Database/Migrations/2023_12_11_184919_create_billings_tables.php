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
        Schema::create('billings_subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('store_id')
                ->index('subscriptions_store_id_index');
            $table->string('shopify_app_subscription_id')
                ->index('subscriptions_shopify_app_subscription_id_index');
            $table->string('shopify_confirmation_url');
            $table->string('status')->default('inactive')->comment('active|inactive');

            $table->dateTime('activated_at')->nullable();
            $table->dateTime('deactivated_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('billings_subscription_line_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('subscription_id')
                ->index('sub_line_items_subscription_id_index');
            $table->string('shopify_app_subscription_id')
                ->index('sub_line_items_shopify_app_subscription_id_index');
            $table->string('shopify_app_subscription_line_item_id')
                ->index('sub_line_items_shopify_app_subscription_line_item_id_index');
            $table->string('terms');
            $table->integer('capped_amount');
            $table->string('capped_amount_currency')->comment('ISO 4217');

            $table->dateTime('activated_at')->nullable();
            $table->dateTime('deactivated_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('billings_app_usage_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('subscription_line_item_id')
                ->index('app_usage_subscription_line_item_id_index');
            $table->string('shopify_app_subscription_line_item_id')
                ->index('app_usage_shopify_app_subscription_line_item_id_index');
            $table->string('description');
            $table->integer('amount');
            $table->string('amount_currency')->comment('ISO 4217');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('billings_app_usage_configs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('store_id')
                ->index('app_usage_config_store_id_index');
            $table->string('subscription_line_item_id')
                ->index('app_usage_config_subscription_line_item_id_index');
            $table->string('description');
            $table->json('config');

            $table->dateTime('activated_at')->nullable();
            $table->dateTime('deactivated_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings_subscriptions');
        Schema::dropIfExists('billings_subscription_line_items');
        Schema::dropIfExists('billings_app_usage_records');
        Schema::dropIfExists('billings_app_usage_configs');
    }
};
