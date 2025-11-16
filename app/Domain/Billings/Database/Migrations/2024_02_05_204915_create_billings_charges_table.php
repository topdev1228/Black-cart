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
        Schema::create('billings_charges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('store_id');
            $table->string('currency', 3);
            $table->integer('amount');
            $table->integer('balance');
            $table->boolean('is_billed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings_charges');
    }
};
