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
        Schema::table('stores', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('state_code')->after('state');
            $table->string('source')->nullable()->after('ecommerce_platform_plan_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('state_code');
            $table->dropColumn('source');
        });
    }
};
