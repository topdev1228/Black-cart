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
        Schema::table('trialables', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('status');
            $table->unsignedSmallInteger('trial_duration')->default(7)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('trialables', function (Blueprint $table) {
            $table->dropColumn('expires_at');
        });
        Schema::table('trialables', function (Blueprint $table) {
            $table->dropColumn('trial_duration');
        });
    }
};
