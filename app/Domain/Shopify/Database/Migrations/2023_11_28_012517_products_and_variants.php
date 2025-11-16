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
        //

        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('source')->default('shopify');
            $table->string('source_id')->unique();
            $table->string('source_handle');
            $table->string('source_online_store_url');

            $table->string('title');
            $table->text('description');
            $table->string('type');
            $table->string('image_url');

            $table->boolean('is_gift_card');
            $table->boolean('requires_selling_plan');

            $table->string('tags')->nullable();
            $table->string('vendor')->nullable();
            $table->integer('max_variant_price')->nullable();
            $table->integer('min_variant_price')->default(0);

            $table->json('data');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('product_id');

            $table->string('source')->default('shopify');
            $table->string('source_id')->unique();
            $table->string('source_product_id');
            $table->string('title');
            $table->string('barcode')->nullable();
            $table->string('sku')->nullable();
            $table->boolean('requires_shipping')->default(true);
            $table->integer('price');
            $table->string('image_url');
            $table->integer('unit_price')->nullable();
            $table->json('unit_price_measurement')->nullable();
            $table->float('weight')->nullable();
            $table->string('weight_unit')->nullable();
            $table->json('data');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //

        Schema::dropIfExists('products');
        Schema::dropIfExists('product_variants');
    }
};
