<?php
declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

trait RefreshDatabase
{
    use LazilyRefreshDatabase;

    /**
     * The parameters that should be used when running "migrate".
     *
     * @return array
     */
    protected function migrateFreshUsing()
    {
        $args = [
            '--seed' => $this->shouldSeed(),
        ];

        if (app()->environment('testing')) {
            $args['--path'] = 'database/migrations/testing';
            $args['--schema-path'] = '/dev/null'; // ensure the schema file isn't picked up
        }

        return $args;
    }
}
