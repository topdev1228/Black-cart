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
        Schema::create('shopify_mandatory_webhooks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('store_id')->index();
            $table->string('topic');
            $table->string('shopify_shop_id');
            $table->string('shopify_domain')->index();
            $table->json('data');
            $table->string('status')->default('pending');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopify_mandatory_webhooks');
    }
};
