<?php
declare(strict_types=1);

namespace Tests\Feature\Domain\Shopify\Http\Controllers;

use App\Domain\Shopify\Services\OAuthService;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Two\User;
use Mockery\MockInterface;
use Tests\TestCase;

class ShopifyControllerTest extends TestCase
{
    public function testItRedirects(): void
    {
        $this->mock(OAuthService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getRedirect')->once()->andReturn(new RedirectResponse('https://test.myshopify.com'));
        });

        $this->get(route('shopify.web.redirect'))->assertRedirect('https://test.myshopify.com');
    }

    public function testItVerifies(): void
    {
        $this->mock(OAuthService::class, function (MockInterface $mock) {
            $user = new User();
            $user->user = ['myshopify_domain' => 'test.myshopify.com'];
            $mock->shouldReceive('verify')->once()->andReturn($user);
        });

        $this->get(route('shopify.web.callback'))->assertRedirect('https://test.myshopify.com' . config('services.shopify.admin_url_path'));
    }
}
