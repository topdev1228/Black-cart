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
        Schema::table('orders_refund_line_items', function (Blueprint $table) {
            $table->renameColumn('refund_id', 'source_refund_reference_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders_refund_line_items', function (Blueprint $table) {
            $table->renameColumn('source_refund_reference_id', 'refund_id');
        });
    }
};
