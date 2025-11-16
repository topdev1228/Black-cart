<?php
declare(strict_types=1);

use App\Domain\Stores\Models\Store;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('test_store_ids', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Store::class);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('test_store_ids');
    }
};
