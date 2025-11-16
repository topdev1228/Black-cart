<?php
declare(strict_types=1);

namespace App\Domain\Billings\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property ShopifyCurrentAppInstallationResource $resource
 */
class ShopifyCurrentAppInstallationResource extends JsonResource
{
    public static $wrap = 'shopify_current_app_installation';
}
