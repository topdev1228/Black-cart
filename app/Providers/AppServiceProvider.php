<?php
declare(strict_types=1);

namespace App\Providers;

use App\Domain\Billings\Events\BillableChargesCreatedEvent;
use App\Domain\Billings\Events\TbybNetSaleCreatedEvent;
use App\Domain\Billings\Listeners\BillMerchantListener;
use App\Domain\Billings\Listeners\CreateChargesListener;
use App\Domain\Shared\Http\Client\Middleware\GraphQLMetrics;
use App\Domain\Shared\Http\Client\Middleware\GraphQLResponse;
use App\Domain\Shared\Http\Client\Middleware\JwtAuthentication;
use App\Domain\Shared\Values\AppContext;
use App\Domain\Shopify\Values\JwtPayload;
use App\Domain\Shopify\Values\JwtToken;
use App\Domain\Stores\Models\Store;
use App\Domain\Stores\Values\Store as StoreValue;
use App\Exceptions\Handler;
use Carbon\CarbonImmutable;
use Event;
use Firebase\JWT\JWT;
use Http;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Stringable;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Shopify\ShopifyExtendSocialite;
use Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Date::use(CarbonImmutable::class);

        $this->registerEvents();
        $this->registerAppContext();
        $this->registerStringMacros();
        $this->registerCollectionMacros();

        $this->registerHttpClientMiddleware();

        $this->loadMigrations();
    }

    protected function registerHttpClientMiddleware(): void
    {
        Http::globalRequestMiddleware(new JwtAuthentication());
        Http::globalRequestMiddleware(new GraphQLMetrics());
        Http::globalMiddleware(new GraphQLResponse());
    }

    protected function registerAppContext(): void
    {
        app()->singleton(AppContext::class, fn () => AppContext::from(AppContext::empty()));
        app()->bind(ExceptionHandler::class, Handler::class);

        App::macro('context', function (StoreValue|Store|null $store = null, ?JwtToken $jwtToken = null): AppContext {
            $context = resolve(AppContext::class);

            if ($store !== null) {
                if ($store instanceof Store) {
                    $store = StoreValue::from($store->toArray());
                }
                $context->store = $store;
            }

            if ($jwtToken !== null) {
                $context->jwtToken = $jwtToken;
            }

            if (empty($context->jwtToken->token)) {
                $context->jwtToken = new JwtToken(JWT::encode(
                    (new JwtPayload(domain: $context->store->domain))->toArray(),
                    config('services.shopify.client_secret'),
                    'HS256'
                ));
            }

            app()->singleton(AppContext::class, fn () => $context);

            return $context;
        });
    }

    protected function registerStringMacros(): void
    {
        Str::macro('shopifyGid', function (string $id, string $type): string {
            return Str::start($id, 'gid://shopify/' . $type . '/');
        });

        Stringable::macro('shopifyGid', function (string $type) {
            /** @psalm-suppress UndefinedThisPropertyFetch */
            return new Stringable(Str::shopifyGid($this->value, $type));
        });

        Str::macro(
            'shopifyId',
            /**
             * @psalm-suppress NullableReturnStatement
             * @psalm-suppress InvalidReturnStatement
             * @psalm-suppress InvalidReturnType
             * @psalm-suppress InvalidNullableReturnType
             */
            function (string $gid): string {
                return Str::replaceMatches('@^gid://shopify/(.*?)/@', '', $gid);
            }
        );

        Stringable::macro('shopifyId', function () {
            /** @psalm-suppress UndefinedThisPropertyFetch */
            return new Stringable(Str::shopifyId($this->value));
        });
    }

    protected function registerCollectionMacros(): void
    {
        // Added in PHP 8.2
        if (!defined('DATE_ISO8601_EXPANDED')) {
            define('DATE_ISO8601_EXPANDED', 'Y-m-d\TH:i:sP');
        }

        Collection::macro('sortByDate', function (string $key, ?string $format = DATE_ISO8601_EXPANDED) {
            /** @var Collection $this */
            return $this->sort(function ($a, $b) use ($format, $key) {
                $dateA = Date::createFromFormat($format, $a[$key]);
                $dateB = Date::createFromFormat($format, $b[$key]);

                return match (true) {
                    $dateA->greaterThan($dateB) => 1,
                    $dateA->lessThan($dateB) => -1,
                    default => 0,
                };
            });
        });

        Collection::macro('sortByDateDesc', function (string $key, ?string $format = DATE_ISO8601_EXPANDED) {
            /** @var Collection $this */
            return $this->sort(function ($a, $b) use ($format, $key) {
                $dateA = Date::createFromFormat($format, $a[$key]);
                $dateB = Date::createFromFormat($format, $b[$key]);

                return match (true) {
                    $dateA->greaterThan($dateB) => -1,
                    $dateA->lessThan($dateB) => 1,
                    default => 0,
                };
            });
        });
    }

    protected function registerEvents(): void
    {
        // Required to enable the Socialite Shopify plugin
        Event::listen(SocialiteWasCalled::class, [ShopifyExtendSocialite::class, 'handle']);

        // These should be moved to pubsub events
        Event::listen(TbybNetSaleCreatedEvent::class, CreateChargesListener::class);
        Event::listen(BillableChargesCreatedEvent::class, BillMerchantListener::class);
    }

    protected function loadMigrations(): void
    {
        $this->loadMigrationsFrom([
            database_path() . '/../app/Domain/Billings/Database/Migrations/',
            database_path() . '/../app/Domain/Orders/Database/Migrations/',
            database_path() . '/../app/Domain/Payments/Database/Migrations/',
            database_path() . '/../app/Domain/Products/Database/Migrations/',
            database_path() . '/../app/Domain/Programs/Database/Migrations/',
            database_path() . '/../app/Domain/Shared/Database/Migrations/',
            database_path() . '/../app/Domain/Shopify/Database/Migrations/',
            database_path() . '/../app/Domain/Stores/Database/Migrations/',
            database_path() . '/../app/Domain/Trials/Database/Migrations/',
        ]);
    }
}
