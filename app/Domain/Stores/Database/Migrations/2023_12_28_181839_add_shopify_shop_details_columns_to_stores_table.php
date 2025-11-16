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
            $table->string('email')->after('domain');
            $table->string('owner_name')->after('email');
            $table->string('currency', 3)->comment('ISO 4127')->after('owner_name');
            $table->string('primary_locale', 2)->comment('ISO 639 two-letter language code')
                ->after('currency');
            $table->string('address1')->after('primary_locale');
            $table->string('address2')->nullable()->after('address1');
            $table->string('city')->after('address2');
            $table->string('state')->after('city');
            $table->string('country')->after('state');
            $table->string('country_code', 3)->comment('ISO 3166-1 alpha-2')
                ->after('country');
            $table->string('country_name')->after('country_code');
            $table->string('iana_timezone')->after('country_name');
            $table->string('ecommerce_platform')->default('shopify')->after('iana_timezone');
            $table->string('ecommerce_platform_store_id')->after('ecommerce_platform');
            $table->string('ecommerce_platform_plan')->after('ecommerce_platform_store_id');
            $table->string('ecommerce_platform_plan_name')->after('ecommerce_platform_plan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropColumn('owner_name');
            $table->dropColumn('currency');
            $table->dropColumn('primary_locale');
            $table->dropColumn('address1');
            $table->dropColumn('address2');
            $table->dropColumn('city');
            $table->dropColumn('state');
            $table->dropColumn('country');
            $table->dropColumn('country_code');
            $table->dropColumn('country_name');
            $table->dropColumn('iana_timezone');
            $table->dropColumn('ecommerce_platform');
            $table->dropColumn('ecommerce_platform_store_id');
            $table->dropColumn('ecommerce_platform_plan');
            $table->dropColumn('ecommerce_platform_plan_name');
        });
    }
};
