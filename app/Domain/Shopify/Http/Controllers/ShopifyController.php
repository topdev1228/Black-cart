<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Http\Controllers;

use App\Domain\Shared\Http\Controllers\Controller;
use App\Domain\Shopify\Services\OAuthService;
use Illuminate\Http\RedirectResponse;

class ShopifyController extends Controller
{
    public function __construct(protected OAuthService $oAuthService)
    {
    }

    public function redirect(): RedirectResponse
    {
        return $this->oAuthService->getRedirect();
    }

    public function callback(): RedirectResponse
    {
        $oauth = $this->oAuthService->verify();

        return redirect('https://' . $oauth->user['myshopify_domain'] . config('services.shopify.admin_url_path'));
    }
}
