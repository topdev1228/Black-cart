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

        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('store_id');
            $table->string('source_id');
            $table->string('status');
            $table->json('order_data');
            $table->json('blackcart_metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('orders_line_items', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('source_id');
            $table->string('order_id');
            $table->integer('quantity');
            $table->string('status');
            $table->string('trialable_id')->nullable();

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
        Schema::dropIfExists('orders');
        Schema::dropIfExists('orders_line_items');
    }
};
