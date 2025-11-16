<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        $dump = database_path('schema/' . $driver . '-schema.sql');
        DB::unprepared(file_get_contents($dump));

        $sqls = glob(database_path('seeders') . '/sql/*.sql');
        sort($sqls, SORT_NUMERIC | SORT_ASC);
        foreach ($sqls as $sql) {
            DB::unprepared(file_get_contents($sql));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // There is no reversal
    }
};
