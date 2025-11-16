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
        Schema::table('shopify_jobs', function (Blueprint $table) {
            $table->string('type')->default('query')->after('query');
            $table->string('topic')->after('domain');
            $table->string('export_partial_file_url')->nullable()->after('export_file_url');
            $table->string('error_code')->nullable()->after('status');
        });

        Schema::table('shopify_jobs', function (Blueprint $table) {
            $table->dropColumn('callback_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shopify_jobs', function (Blueprint $table) {
            $table->string('callback_url')->after('domain');
        });

        Schema::table('shopify_jobs', function (Blueprint $table) {
            $table->dropColumn('error_code');
        });
        Schema::table('shopify_jobs', function (Blueprint $table) {
            $table->dropColumn('export_partial_file_url');
        });
        Schema::table('shopify_jobs', function (Blueprint $table) {
            $table->dropColumn('topic');
        });
        Schema::table('shopify_jobs', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
