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
        Schema::table('billings_subscriptions', function (Blueprint $table) {
            $table->string('status')->default('pending')->comment('')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billings_subscriptions', function (Blueprint $table) {
            $table->string('status')->default('inactive')->comment('active|inactive')->change();
        });
    }
};
