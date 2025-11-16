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
        Schema::create('store_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('value');
            $table->boolean('is_secure')->default(false);
            $table->foreignIdFor(Store::class);
            $table->timestamps();
            $table->unique(['store_id', 'name']);
            $table->index('store_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
