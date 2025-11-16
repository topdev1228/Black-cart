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
        Schema::create('orders_trial_group', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('order_id');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('orders_line_items', function (Blueprint $table) {
            $table->string('trial_group_id')->nullable()->after('trialable_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('orders_trial_group');
        Schema::table('orders_line_items', function (Blueprint $table) {
            $table->dropColumn('trial_group_id');
        });
    }
};
