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
        Schema::create('trialables', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('source_key')->default('shopify');
            $table->string('source_id');
            $table->string('group_key')->nullable();

            $table->string('status')->default('init');

            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('image')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trialables');
    }
};
