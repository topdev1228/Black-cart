<?php declare(strict_types=1);

/*
 * This must return a map from endpoint names to EndpointConfig classes.
 */

use App\Domain\Shared\Http\Client\ShopifyConfig;
use Illuminate\Support\Str;

return collect(glob(__DIR__ . '/app/Domain/*/GraphQL/Queries/Shopify/*'))->mapWithKeys(function (string $directory) {
    $dir = Str::of($directory);
    $domain = $dir
        ->betweenFirst('Domain/', '/GraphQL')
        ->toString();
    $version = $dir->basename()->toString();
    $key = Str::of($domain)
        ->betweenFirst('Domain/', '/GraphQL')
        ->prepend($dir->betweenFirst('Queries/', '/'), '-')
        ->append('-', $version)
        ->lower()
        ->toString();

    return [
        $key => new ShopifyConfig($domain, $version, !$dir->basename()->is('Unstable')),
    ];
})->toArray();
