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
        Schema::table('billings_charges', function (Blueprint $table) {
            $table->dateTime('time_range_start')->after('is_billed')->nullable();
            $table->dateTime('time_range_end')->after('time_range_start')->nullable();
            $table->string('tbyb_net_sale_id')->after('store_id')->nullable();
            $table->dateTime('billed_at')->after('is_billed')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billings_charges', function (Blueprint $table) {
            $table->dropColumn('billed_at');
            $table->dropColumn('tbyb_net_sale_id');
            $table->dropColumn('time_range_end');
            $table->dropColumn('time_range_start');
        });
    }
};
