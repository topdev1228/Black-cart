<?php
declare(strict_types=1);

use App\Domain\Orders\Enums\LineItemDecisionStatus;
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
            $table->string('decision_status')->default(LineItemDecisionStatus::KEPT->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders_line_items', function (Blueprint $table) {
            $table->dropColumn('decision_status');
        });
    }
};
