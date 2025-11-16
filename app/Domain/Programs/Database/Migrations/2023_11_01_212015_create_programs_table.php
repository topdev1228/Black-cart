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
        Schema::create('programs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->default('Try Before You Buy');
            $table->string('store_id')->index();
            $table->string('shopify_selling_plan_group_id')->nullable();
            $table->string('shopify_selling_plan_id')->nullable();
            $table->integer('try_period_days')->default(7);
            $table->string('deposit_type')->default('fixed')->comment('Supports fixed or percentage');
            $table->integer('deposit_value')->default(0)->comment('Value in cents if deposit_type = fixed');
            $table->integer('min_tbyb_items')->default(1);
            $table->integer('max_tbyb_items')->nullable()->comment('NULL means unlimited');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
