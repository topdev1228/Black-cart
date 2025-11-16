<?php
declare(strict_types=1);

namespace App\Flags;

use App;
use App\Domain\Stores\Values\Store as StoreValue;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use MikeFrancis\LaravelUnleash\Strategies\Contracts\DynamicStrategy;

/**
 * Class StoreWithDomainsStrategy
 * This is a custom strategy for our feature flag base on the stores.domain database table and column.
 * the strategy take in a list of domains defined in the "storeDomains" params and check if the given store object has
 * a domain that is within the list.
 *
 * example use case:
 *   - add a flag to turn on a feature for heelboy (storeDomains: 'heelboy.myshopify.com')
 *   - add the word "eh!" in email templates for Canadian merchants (storeDomains : 'heelboy.myshopify.com, sleep-science.myshopify.com')
 */
class StoreWithDomainsStrategy implements DynamicStrategy
{
    public function isEnabled(array $params, Request $request, ...$args): bool
    {
        $store = $args[0] ?? null;
        if (!$store instanceof StoreValue) {
            $store = App::context()->store;
            if ($store === null) {
                return false;
            }
        }

        $storeDomains = explode(',', (string) Arr::get($params, 'storeDomains', ''));

        return in_array($store->domain, $storeDomains);
    }
}
