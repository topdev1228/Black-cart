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
        Schema::table('orders_line_items', function (Blueprint $table) {
            $table->string('source_order_id')->nullable()->after('order_id')->index();
            $table->string('source_product_id')->nullable()->after('source_id')->index();
            $table->string('source_variant_id')->nullable()->after('source_product_id')->index();
            $table->string('product_title')->nullable()->after('source_variant_id');
            $table->string('variant_title')->nullable()->after('product_title');
            $table->string('thumbnail')->nullable()->after('variant_title');

            $table->index('order_id', 'orders_line_items_order_id_index');
            $table->index('source_id', 'orders_line_items_source_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders_line_items', function (Blueprint $table) {
            $table->dropColumn('source_order_id');
            $table->dropColumn('source_product_id');
            $table->dropColumn('source_variant_id');
            $table->dropColumn('product_title');
            $table->dropColumn('variant_title');
            $table->dropColumn('thumbnail');

            $table->dropIndex('orders_line_items_order_id_index');
            $table->dropIndex('orders_line_items_source_id_index');
        });
    }
};
