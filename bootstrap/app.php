<?php

use App\Domain\Shared\Http\Middleware\AccessControlHeaders;
use App\Domain\Shared\Http\Middleware\AuthenticateGooglePubSub;
use App\Domain\Shared\Http\Middleware\CspHeader;
use App\Domain\Shared\Http\Middleware\EnsureShopifyInstalled;
use App\Domain\Shared\Http\Middleware\EnsureShopifySession;
use App\Domain\Shared\Http\Middleware\HandleGooglePubSubMessage;
use App\Domain\Shared\Http\Middleware\JwtAuthentication;
use App\Domain\Shared\Http\Middleware\LogShopifyWebhook;
use App\Domain\Shared\Http\Middleware\PreventRequestsDuringMaintenance;
use App\Domain\Shared\Http\Middleware\TrimStrings;
use App\Domain\Shared\Http\Middleware\TrustProxies;
use App\Enums\Exceptions\ApiExceptionErrorCodes;
use App\Enums\Exceptions\ApiExceptionTypes;
use App\Exceptions\ApiException;
use App\Exceptions\NotFoundException;
use App\Providers\AppServiceProvider;
use Illuminate\Auth\AuthenticationException as LaravelAuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use MikeFrancis\LaravelUnleash\ServiceProvider as UnleashServiceProvider;
use SocialiteProviders\Manager\ServiceProvider as SocialiteServiceProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        UnleashServiceProvider::class,
        SocialiteServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        // api: __DIR__.'/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        // channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn() => route('login'));
        $middleware->redirectUsersTo(AppServiceProvider::HOME);

        $middleware->append([
            TrustProxies::class,
            PreventRequestsDuringMaintenance::class,
            TrimStrings::class,
        ]);

        $middleware->web([
            AccessControlHeaders::class,
            CspHeader::class,
        ]);

        $middleware->api(JwtAuthentication::class);

        $middleware->group('pubsub', [
            AuthenticateGooglePubSub::class,
            HandleGooglePubSubMessage::class,
            LogShopifyWebhook::class,
        ]);

        $middleware->alias([
            'auth.pubsub' => AuthenticateGooglePubSub::class,
            'shopify.auth' => EnsureShopifySession::class,
            'shopify.installed' => EnsureShopifyInstalled::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (Throwable $e) {
            //
        });

        $exceptions->renderable(function (ValidationException $exception, Request $request) {
            return response()->json([
                'type' => ApiExceptionTypes::REQUEST_ERROR,
                'code' => ApiExceptionErrorCodes::INVALID_PARAMETERS,
                'message' => $exception->getMessage(),
                'errors' => $exception->errors(),
            ], $exception->status);
        });

        $exceptions->renderable(function (LaravelAuthenticationException $exception, Request $request) use ($exceptions) {
            return $request->expectsJson()
                ? $exceptions->render(fn(ApiException $e, Request $request) => false)
                : redirect()->guest($exception->redirectTo($request) ?? route('login'));
        });

        $exceptions->renderable(function (NotFoundHttpException $exception, Request $request) use ($exceptions) {
            // $exception->getMessage() can be with ID or without ID like below, respectively:
            //  "No query results for model [App\Domain\Programs\Models\Program] non_existent_program_id" OR
            //  "No query results for model [App\Domain\Programs\Models\Program]."
            //  This regex will get the model name, in this example, "Program", and ignore the rest, including the ID as
            //  we may not want to expose internal IDs.

            preg_match(
                '/No query results for model \[.*\\\(.*)\]/',
                $exception->getMessage(),
                $matches
            );
            $model = $matches[1];

            $errorCode = ApiExceptionErrorCodes::tryFrom((string) Str::of($model)->lower()->append('_not_found'));
            if ($errorCode === null) {
                $errorCode = ApiExceptionErrorCodes::RESOURCE_NOT_FOUND;
            }

            return $exceptions->render(
                fn(NotFoundException $e, Request $request) => false
            );
        });
    })
    ->withCommands([
        __DIR__.'/../app/Domain/Orders/Console/Commands',
        __DIR__.'/../app/Domain/Shopify/Console/Commands',
    ])
    ->create();
