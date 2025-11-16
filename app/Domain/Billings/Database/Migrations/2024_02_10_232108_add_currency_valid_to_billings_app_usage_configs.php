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
        Schema::table('billings_app_usage_configs', function (Blueprint $table) {
            $table->string('currency', 3);
            $table->dateTime('valid_from');
            $table->dateTime('valid_to')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billings_app_usage_configs', function (Blueprint $table) {
            $table->dropColumn('currency');
            $table->dropColumn('valid_from');
            $table->dropColumn('valid_to');
        });
    }
};
