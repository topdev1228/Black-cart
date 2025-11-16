<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Shared\Http\Controllers;

use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Support\Env;
use Log;
use Tests\TestCase;

class AppVerifyControllerTest extends TestCase
{
    use MakesHttpRequests;

    public function testItReturnsSecretVerificationFromEnv(): void
    {
        Env::enablePutenv();

        putenv('SECRET_VERIFICATION=true');

        $this->getJson('/api/app/secrets')
            ->assertOk()
            ->assertJson(['SECRET_VERIFICATION' => true]);

        Env::disablePutenv();
    }

    public function testItReturnsSecretVerificationWithoutEnv(): void
    {
        $this->getJson('/api/app/secrets')
            ->assertOk()
            ->assertJson(['SECRET_VERIFICATION' => false]);
    }

    public function testItReturnsHeaders(): void
    {
        $this->getJson('/api/app/headers', ['X-Test' => 'test'])
            ->assertOk()
            ->assertJson(['x-test' => ['test']]);
    }

    public function testItLogs(): void
    {
        Log::shouldReceive('info')->withArgs(['Log Test', ['test' => 1]])
            ->once();

        $this->getJson('/api/app/log')
            ->assertOk();
    }
}
