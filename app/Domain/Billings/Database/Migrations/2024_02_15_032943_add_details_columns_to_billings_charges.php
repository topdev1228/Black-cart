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
            $table->integer('step_size')->nullable();
            $table->integer('step_start_amount')->nullable();
            $table->integer('step_end_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billings_charges', function (Blueprint $table) {
            $table->dropColumn('step_size');
            $table->dropColumn('step_start_amount');
            $table->dropColumn('step_end_amount');
        });
    }
};
