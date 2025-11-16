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
        if (!Schema::hasColumn('shopify_jobs', 'domain')) {
            Schema::table('shopify_jobs', function (Blueprint $table) {
                $table->string('domain')->after('query');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
