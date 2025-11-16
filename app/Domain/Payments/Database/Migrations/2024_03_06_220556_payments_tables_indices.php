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
        Schema::table('payments_transactions', function (Blueprint $table) {
            $table->unique('source_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments_transactions', function (Blueprint $table) {
            $table->dropIndex('payments_transactions_source_id_unique');
        });
    }
};
