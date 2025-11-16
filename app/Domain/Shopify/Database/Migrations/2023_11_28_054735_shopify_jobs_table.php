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
        Schema::create('shopify_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('store_id')->index();
            $table->string('shopify_job_id')->index();
            $table->string('query');
            $table->string('domain');
            $table->string('callback_url');
            $table->string('export_file_url')->nullable();
            $table->string('status');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('shopify_jobs');
    }
};
