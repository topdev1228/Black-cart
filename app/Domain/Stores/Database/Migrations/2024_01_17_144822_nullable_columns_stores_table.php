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
            $table->string('email')->nullable()->change();
            $table->string('owner_name')->nullable()->change();
            $table->string('currency', 3)->comment('ISO 4127')->nullable()->change();
            $table->string('primary_locale', 2)->comment('ISO 639 two-letter language code')
                ->nullable()->change();
            $table->string('address1')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->string('state')->nullable()->change();
            $table->string('state_code')->nullable()->change();
            $table->string('country')->nullable()->change();
            $table->string('country_code', 3)->comment('ISO 3166-1 alpha-2')->nullable()->change();
            $table->string('country_name')->nullable()->change();
            $table->string('iana_timezone')->nullable()->change();
            $table->string('ecommerce_platform_plan')->nullable()->change();
            $table->string('ecommerce_platform_plan_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->string('owner_name')->nullable(false)->change();
            $table->string('currency', 3)->comment('ISO 4127')->nullable(false)->change();
            $table->string('primary_locale', 2)->comment('ISO 639 two-letter language code')
                ->nullable(false)->change();
            $table->string('address1')->after('primary_locale')->nullable(false)->change();
            $table->string('city')->nullable(false)->change();
            $table->string('state')->nullable(false)->change();
            $table->string('state_code')->nullable(false)->change();
            $table->string('country')->nullable(false)->change();
            $table->string('country_code', 3)->comment('ISO 3166-1 alpha-2')->nullable(false)
                ->change();
            $table->string('country_name')->nullable(false)->change();
            $table->string('iana_timezone')->nullable(false)->change();
            $table->string('ecommerce_platform_plan')->nullable(false)->change();
            $table->string('ecommerce_platform_plan_name')->nullable(false)->change();
        });
    }
};
