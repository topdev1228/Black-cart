<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Billings\Services;

use App;
use App\Domain\Billings\Enums\SubscriptionStatus;
use App\Domain\Billings\Events\SubscriptionActivatedEvent;
use App\Domain\Billings\Events\SubscriptionDeactivatedEvent;
use App\Domain\Billings\Exceptions\ActiveSubscriptionNotFoundException;
use App\Domain\Billings\Models\Subscription;
use App\Domain\Billings\Repositories\SubscriptionRepository;
use App\Domain\Billings\Services\SubscriptionService;
use App\Domain\Billings\Values\Subscription as SubscriptionValue;
use App\Domain\Billings\Values\SubscriptionLineItem as SubscriptionLineItemValue;
use App\Domain\Billings\Values\WebhookShopifyAppSubscription;
use App\Domain\Billings\Values\WebhookShopifyAppSubscriptionsUpdate;
use App\Domain\Stores\Models\Store;
use Carbon\CarbonImmutable;
use Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Fixtures\Domains\Billings\Traits\ShopifyAppSubscriptionCreateResponsesTestData;
use Tests\Fixtures\Domains\Billings\Traits\ShopifyQueryAppSubscriptionResponsesTestData;
use Tests\TestCase;

class SubscriptionServiceTest extends TestCase
{
    use ShopifyAppSubscriptionCreateResponsesTestData;
    use ShopifyQueryAppSubscriptionResponsesTestData;

    protected Store $currentStore;
    protected SubscriptionService $subscriptionService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::factory()->create();
        App::context(store: $this->currentStore);

        $this->subscriptionService = resolve(SubscriptionService::class);
    }

    public function testItGetsActiveSubscription(): void
    {
        Subscription::factory()->withShopifyData()->cancelled()->create(['store_id' => $this->currentStore->id]);
        Subscription::factory()->withShopifyData()->declined()->create(['store_id' => $this->currentStore->id]);
        Subscription::factory()->withShopifyData()->expired()->create(['store_id' => $this->currentStore->id]);
        Subscription::factory()->withShopifyData()->frozen()->create(['store_id' => $this->currentStore->id]);
        Subscription::factory()->withShopifyData()->pending()->create(['store_id' => $this->currentStore->id]);
        $subscription = Subscription::factory()->withShopifyData()->active()->create(['store_id' => $this->currentStore->id]);

        $actualSubscription = $this->subscriptionService->getActiveSubscription($this->currentStore->id);

        $this->assertEquals($subscription->id, $actualSubscription->id);
        $this->assertEquals($subscription->store_id, $actualSubscription->storeId);
        $this->assertEquals($subscription->shopify_app_subscription_id, $actualSubscription->shopifyAppSubscriptionId);
        $this->assertEquals($subscription->shopify_confirmation_url, $actualSubscription->shopifyConfirmationUrl);
        $this->assertEquals($subscription->status, $actualSubscription->status);
        $this->assertEquals($subscription->activated_at, $actualSubscription->activatedAt);
        $this->assertEquals($subscription->deactivated_at, $actualSubscription->deactivatedAt);
    }

    public function testItErrorsWithNoActiveSubscription(): void
    {
        Subscription::factory()->withShopifyData()->cancelled()->create(['store_id' => $this->currentStore->id]);
        Subscription::factory()->withShopifyData()->declined()->create(['store_id' => $this->currentStore->id]);
        Subscription::factory()->withShopifyData()->expired()->create(['store_id' => $this->currentStore->id]);
        Subscription::factory()->withShopifyData()->frozen()->create(['store_id' => $this->currentStore->id]);
        Subscription::factory()->withShopifyData()->pending()->create(['store_id' => $this->currentStore->id]);

        $this->expectException(ActiveSubscriptionNotFoundException::class);

        $this->subscriptionService->getActiveSubscription($this->currentStore->id);
    }

    public function testItCreatesSubscription(): void
    {
        $subscriptionValue = SubscriptionValue::builder()->create([
            'store_id' => $this->currentStore->id,
        ]);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifyAppSubscriptionCreateSuccessResponse()),
        ]);

        $expectedSubscriptionValue = SubscriptionValue::builder()->withShopifyData()->create([
            'store_id' => $this->currentStore->id,
        ]);
        $expectedRecurringSubscriptionLineItem = SubscriptionLineItemValue::builder()->recurringSubscription()->create();
        $expectedUsageSubscriptionLineItem = SubscriptionLineItemValue::builder()->create();

        $actualSubscription = $this->subscriptionService->create($subscriptionValue, $this->currentStore->domain);

        Http::assertSent(function (Request $request) {
            // TODO: validate graphql query
            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });

        $this->assertNotEmpty($actualSubscription->id);
        $this->assertEquals($expectedSubscriptionValue->storeId, $actualSubscription->storeId);
        $this->assertEquals($expectedSubscriptionValue->shopifyAppSubscriptionId, $actualSubscription->shopifyAppSubscriptionId);
        $this->assertEquals($expectedSubscriptionValue->shopifyConfirmationUrl, $actualSubscription->shopifyConfirmationUrl);
        $this->assertEquals($expectedSubscriptionValue->status, $actualSubscription->status);
        $this->assertEquals($expectedSubscriptionValue->activatedAt, $actualSubscription->activatedAt);
        $this->assertEquals($expectedSubscriptionValue->deactivatedAt, $actualSubscription->deactivatedAt);

        foreach (
            [$expectedRecurringSubscriptionLineItem, $expectedUsageSubscriptionLineItem] as $i => $expectedLineItem
        ) {
            $actualLineItem = $actualSubscription->subscriptionLineItems[$i];

            $this->assertNotEmpty($actualLineItem->id);
            $this->assertNotEmpty($actualLineItem->subscriptionId);
            $this->assertEquals($expectedLineItem->shopifyAppSubscriptionId, $actualLineItem->shopifyAppSubscriptionId);
            $this->assertEquals($expectedLineItem->shopifyAppSubscriptionLineItemId, $actualLineItem->shopifyAppSubscriptionLineItemId);
            $this->assertEquals($expectedLineItem->type, $actualLineItem->type);
            $this->assertEquals($expectedLineItem->terms, $actualLineItem->terms);
            $this->assertEquals($expectedLineItem->recurringAmount, $actualLineItem->recurringAmount);
            $this->assertEquals($expectedLineItem->recurringAmountCurrency, $actualLineItem->recurringAmountCurrency);
            $this->assertEquals($expectedLineItem->usageCappedAmount, $actualLineItem->usageCappedAmount);
            $this->assertEquals($expectedLineItem->usageCappedAmountCurrency, $actualLineItem->usageCappedAmountCurrency);
        }
    }

    public function testItDoesNotGetByShopifyAppSubscriptionIdOnNonExistentSubscription(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->subscriptionService->getByShopifyAppSubscriptionId('non-existent-shopify-app-subscription-id');
    }

    public function testItGetsByShopifyAppSubscriptionId(): void
    {
        $subscription = Subscription::factory()->withShopifyData()->create(['store_id' => $this->currentStore->id]);

        $actualSubscription = $this->subscriptionService->getByShopifyAppSubscriptionId($subscription->shopify_app_subscription_id);

        $this->assertEquals($subscription->id, $actualSubscription->id);
        $this->assertEquals($subscription->store_id, $actualSubscription->storeId);
        $this->assertEquals($subscription->shopify_app_subscription_id, $actualSubscription->shopifyAppSubscriptionId);
        $this->assertEquals($subscription->shopify_confirmation_url, $actualSubscription->shopifyConfirmationUrl);
        $this->assertEquals($subscription->status, $actualSubscription->status);
        $this->assertEquals($subscription->activated_at, $actualSubscription->activatedAt);
        $this->assertEquals($subscription->deactivated_at, $actualSubscription->deactivatedAt);
    }

    public function testItFailsUpdateSubscriptionFromShopifyWebhookOnNonExistentSubscription(): void
    {
        Event::fake([
            SubscriptionActivatedEvent::class,
            SubscriptionDeactivatedEvent::class,
        ]);

        $input = WebhookShopifyAppSubscriptionsUpdate::builder()->create([
            'id' => 'non-existent-shopify-app-subscription-id',
            'status' => SubscriptionStatus::ACTIVE,
        ]);

        $this->expectException(ModelNotFoundException::class);

        $this->subscriptionService->updateFromShopifyWebhook($input);

        Event::assertNotDispatched(SubscriptionActivatedEvent::class);
        Event::assertNotDispatched(SubscriptionDeactivatedEvent::class);
    }

    public function testItDoesNotUpdateUnchangedSubscriptionFromShopifyWebhook(): void
    {
        Event::fake([
            SubscriptionActivatedEvent::class,
            SubscriptionDeactivatedEvent::class,
        ]);

        $this->partialMock(SubscriptionRepository::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('update');
        });

        $subscription = Subscription::withoutEvents(function () {
            return Subscription::factory()->withShopifyData()->create(['store_id' => $this->currentStore->id]);
        });

        $input = WebhookShopifyAppSubscriptionsUpdate::builder()->create([
            'app_subscription' => WebhookShopifyAppSubscription::builder()->create([
                'id' => $subscription->shopify_app_subscription_id,
                'status' => $subscription->status,
            ]),
        ]);

        $subscriptionUpdatedValue = $this->subscriptionService->updateFromShopifyWebhook($input);

        $this->assertEquals($subscription->status, $subscriptionUpdatedValue->status);

        Event::assertNotDispatched(SubscriptionActivatedEvent::class);
        Event::assertNotDispatched(SubscriptionDeactivatedEvent::class);
    }

    public function testItDoesNotUpdateSameCurrentPeriodEndOnActiveSubscriptionFromShopifyWebhook(): void
    {
        Event::fake([
            SubscriptionActivatedEvent::class,
            SubscriptionDeactivatedEvent::class,
        ]);

        $this->partialMock(SubscriptionRepository::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('update');
        });

        $currentPeriodEnd = '2024-03-18T17:00:00Z';

        $subscription = Subscription::withoutEvents(function () {
            return Subscription::factory()->withShopifyData()->active()->create([
                'store_id' => $this->currentStore->id,
                'current_period_end' => '2024-03-18 17:00:00',
            ]);
        });

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifyQueryAppSubscriptionCurrentPeriodEndSuccessResponse(
                    $subscription->shopify_app_subscription_id,
                    $currentPeriodEnd,
                )),
        ]);

        $input = WebhookShopifyAppSubscriptionsUpdate::builder()->create([
            'app_subscription' => WebhookShopifyAppSubscription::builder()->create([
                'id' => $subscription->shopify_app_subscription_id,
                'status' => SubscriptionStatus::ACTIVE,
            ]),
        ]);

        $subscriptionUpdatedValue = $this->subscriptionService->updateFromShopifyWebhook($input);

        $this->assertEquals(SubscriptionStatus::ACTIVE, $subscriptionUpdatedValue->status);
        $this->assertEquals(new CarbonImmutable($currentPeriodEnd), $subscriptionUpdatedValue->currentPeriodEnd);

        Event::assertNotDispatched(SubscriptionActivatedEvent::class);
        Event::assertNotDispatched(SubscriptionDeactivatedEvent::class);
    }

    public function testItUpdatesCurrentPeriodEndOnActiveSubscriptionFromShopifyWebhook(): void
    {
        Event::fake([
            SubscriptionActivatedEvent::class,
            SubscriptionDeactivatedEvent::class,
        ]);

        $subscription = Subscription::withoutEvents(function () {
            return Subscription::factory()->withShopifyData()->active()
                ->create(['store_id' => $this->currentStore->id]);
        });

        $currentPeriodEnd = '2024-03-18T17:00:00Z';

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifyQueryAppSubscriptionCurrentPeriodEndSuccessResponse(
                    $subscription->shopify_app_subscription_id,
                    $currentPeriodEnd,
                )),
        ]);

        $input = WebhookShopifyAppSubscriptionsUpdate::builder()->create([
            'app_subscription' => WebhookShopifyAppSubscription::builder()->create([
                'id' => $subscription->shopify_app_subscription_id,
                'status' => SubscriptionStatus::ACTIVE,
            ]),
        ]);

        $subscriptionUpdatedValue = $this->subscriptionService->updateFromShopifyWebhook($input);

        $this->assertEquals(SubscriptionStatus::ACTIVE, $subscriptionUpdatedValue->status);
        $this->assertEquals(new CarbonImmutable($currentPeriodEnd), $subscriptionUpdatedValue->currentPeriodEnd);

        Event::assertNotDispatched(SubscriptionActivatedEvent::class);
        Event::assertNotDispatched(SubscriptionDeactivatedEvent::class);
    }

    #[DataProvider('subscriptionStatusProvider')]
    public function testItUpdatesSubscriptionFromShopifyWebhook(SubscriptionStatus $status): void
    {
        Event::fake([
            SubscriptionActivatedEvent::class,
            SubscriptionDeactivatedEvent::class,
        ]);

        $subscription = Subscription::withoutEvents(function () {
            return Subscription::factory()->withShopifyData()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });

        if ($status === SubscriptionStatus::ACTIVE) {
            Http::fake([
                App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                    ->push(static::getShopifyQueryAppSubscriptionCurrentPeriodEndSuccessResponse(
                        $subscription->shopify_app_subscription_id,
                        '2024-02-19T17:00:00Z',
                    )),
            ]);
        }

        $updatedAt = Date::now();

        $input = WebhookShopifyAppSubscriptionsUpdate::builder()->create([
            'app_subscription' => WebhookShopifyAppSubscription::builder()->create([
                'id' => $subscription->shopify_app_subscription_id,
                'status' => $status,
                'updatedAt' => $updatedAt,
            ]),
        ]);

        $subscriptionUpdatedValue = $this->subscriptionService->updateFromShopifyWebhook($input);

        $this->assertEquals($subscription->id, $subscriptionUpdatedValue->id);
        $this->assertEquals($input->appSubscription->status, $subscriptionUpdatedValue->status);

        if ($status === SubscriptionStatus::ACTIVE) {
            $this->assertEquals($input->appSubscription->updatedAt, $subscriptionUpdatedValue->activatedAt);
            $this->assertEquals(new CarbonImmutable('2024-02-19T17:00:00Z'), $subscriptionUpdatedValue->currentPeriodEnd);
            $this->assertTrue($updatedAt->copy()->addDays($subscriptionUpdatedValue->trialDays)
                ->isSameSecond($subscriptionUpdatedValue->trialPeriodEnd));

            Event::assertDispatched(
                SubscriptionActivatedEvent::class,
                function (SubscriptionActivatedEvent $event) use ($subscriptionUpdatedValue) {
                    $this->assertEquals($subscriptionUpdatedValue->id, $event->subscription->id);
                    $this->assertEquals($subscriptionUpdatedValue->status, $event->subscription->status);

                    return true;
                },
            );
            Event::assertNotDispatched(SubscriptionDeactivatedEvent::class);

            Http::assertSent(function (Request $request) use ($subscription) {
                $body = json_decode($request->body(), true);
                $this->assertEquals($subscription->shopify_app_subscription_id, $body['variables']['id']);

                return $request->method() === 'POST' &&
                    $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
            });
        } elseif (in_array($status, [
            SubscriptionStatus::CANCELLED, SubscriptionStatus::EXPIRED,
            SubscriptionStatus::FROZEN, SubscriptionStatus::DECLINED,
        ])) {
            $this->assertEquals($input->appSubscription->updatedAt, $subscriptionUpdatedValue->deactivatedAt);
        }

        // If the subscription is a terminal status, the subscription is soft deleted in the database
        if (in_array($status, [SubscriptionStatus::CANCELLED, SubscriptionStatus::EXPIRED, SubscriptionStatus::DECLINED])) {
            $deletedSubscription = Subscription::withTrashed()->findOrFail($subscription->id);
            $this->assertEquals($subscriptionUpdatedValue->deactivatedAt, $deletedSubscription->deleted_at);
        }

        if ($subscription->status !== $input->appSubscription->status && $status !== SubscriptionStatus::ACTIVE) {
            Event::assertNotDispatched(SubscriptionActivatedEvent::class);
            Event::assertNotDispatched(SubscriptionDeactivatedEvent::class);
        }
    }

    public function testItUpdatesSubscriptionFromShopifyWebhookNoTrial(): void
    {
        Event::fake([
            SubscriptionActivatedEvent::class,
            SubscriptionDeactivatedEvent::class,
        ]);

        $subscription = Subscription::withoutEvents(function () {
            return Subscription::factory()->withShopifyData()->noTrial()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifyQueryAppSubscriptionCurrentPeriodEndSuccessResponse()),
        ]);

        $updatedAt = Date::now();

        $input = WebhookShopifyAppSubscriptionsUpdate::builder()->create([
            'app_subscription' => WebhookShopifyAppSubscription::builder()->create([
                'id' => $subscription->shopify_app_subscription_id,
                'status' => SubscriptionStatus::ACTIVE,
                'updatedAt' => $updatedAt,
            ]),
        ]);

        $subscriptionUpdatedValue = $this->subscriptionService->updateFromShopifyWebhook($input);

        $this->assertEquals($subscription->id, $subscriptionUpdatedValue->id);
        $this->assertEquals($input->appSubscription->status, $subscriptionUpdatedValue->status);

        $this->assertEquals($input->appSubscription->updatedAt, $subscriptionUpdatedValue->activatedAt);
        $this->assertEquals(new CarbonImmutable('2024-02-19T17:00:00Z'), $subscriptionUpdatedValue->currentPeriodEnd);
        $this->assertNull($subscriptionUpdatedValue->trialPeriodEnd);

        Event::assertDispatched(
            SubscriptionActivatedEvent::class,
            function (SubscriptionActivatedEvent $event) use ($subscriptionUpdatedValue) {
                $this->assertEquals($subscriptionUpdatedValue->id, $event->subscription->id);
                $this->assertEquals($subscriptionUpdatedValue->status, $event->subscription->status);

                return true;
            },
        );
        Event::assertNotDispatched(SubscriptionDeactivatedEvent::class);

        Http::assertSent(function (Request $request) use ($subscription) {
            $body = json_decode($request->body(), true);
            $this->assertEquals($subscription->shopify_app_subscription_id, $body['variables']['id']);

            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public function testItDoesNotUpdateUnchangedSubscriptionStatus(): void
    {
        Event::fake([
            SubscriptionActivatedEvent::class,
            SubscriptionDeactivatedEvent::class,
        ]);

        $this->partialMock(SubscriptionRepository::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('update');
        });

        $subscription = Subscription::withoutEvents(function () {
            return Subscription::factory()->withShopifyData()->create(['store_id' => $this->currentStore->id]);
        });
        $subscriptionValue = SubscriptionValue::from($subscription);
        $updatedAt = Date::now();

        $subscriptionUpdatedValue = $this->subscriptionService
            ->updateStatus($subscriptionValue, $subscription->status, $updatedAt);

        $this->assertEquals($subscription->status, $subscriptionUpdatedValue->status);

        Event::assertNotDispatched(SubscriptionActivatedEvent::class);
        Event::assertNotDispatched(SubscriptionDeactivatedEvent::class);
    }

    #[DataProvider('subscriptionStatusProvider')]
    public function testItUpdatesSubscriptionStatus(SubscriptionStatus $status): void
    {
        Event::fake([
            SubscriptionActivatedEvent::class,
            SubscriptionDeactivatedEvent::class,
        ]);

        $subscription = Subscription::withoutEvents(function () {
            return Subscription::factory()->withShopifyData()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });
        $subscriptionValue = SubscriptionValue::from($subscription);
        $updatedAt = Date::now();

        if ($status === SubscriptionStatus::ACTIVE) {
            Http::fake([
                App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                    ->push(static::getShopifyQueryAppSubscriptionCurrentPeriodEndSuccessResponse()),
            ]);
        }

        $subscriptionUpdatedValue = $this->subscriptionService->updateStatus($subscriptionValue, $status, $updatedAt);

        $this->assertEquals($subscription->id, $subscriptionUpdatedValue->id);
        $this->assertEquals($status, $subscriptionUpdatedValue->status);

        if ($status === SubscriptionStatus::ACTIVE) {
            $this->assertTrue($updatedAt->isSameSecond($subscriptionUpdatedValue->activatedAt));
            $this->assertEquals(new CarbonImmutable('2024-02-19T17:00:00Z'), $subscriptionUpdatedValue->currentPeriodEnd);
            $this->assertTrue($updatedAt->copy()->addDays($subscriptionValue->trialDays)
                ->isSameSecond($subscriptionUpdatedValue->trialPeriodEnd));
            Event::assertDispatched(
                SubscriptionActivatedEvent::class,
                function (SubscriptionActivatedEvent $event) use ($subscriptionUpdatedValue) {
                    $this->assertEquals($subscriptionUpdatedValue->id, $event->subscription->id);
                    $this->assertEquals($subscriptionUpdatedValue->status, $event->subscription->status);

                    return true;
                },
            );
            Event::assertNotDispatched(SubscriptionDeactivatedEvent::class);

            Http::assertSent(function (Request $request) use ($subscription) {
                $body = json_decode($request->body(), true);
                $this->assertEquals($subscription->shopify_app_subscription_id, $body['variables']['id']);

                return $request->method() === 'POST' &&
                    $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
            });
        } elseif (in_array($status, [
            SubscriptionStatus::CANCELLED, SubscriptionStatus::EXPIRED,
            SubscriptionStatus::FROZEN, SubscriptionStatus::DECLINED,
        ])) {
            $this->assertTrue($updatedAt->isSameSecond($subscriptionUpdatedValue->deactivatedAt));
        }

        // If the subscription is a terminal status, the subscription is soft deleted in the database
        if (in_array($status, [SubscriptionStatus::CANCELLED, SubscriptionStatus::EXPIRED, SubscriptionStatus::DECLINED])) {
            $deletedSubscription = Subscription::withTrashed()->findOrFail($subscription->id);
            $this->assertEquals($subscriptionUpdatedValue->deactivatedAt, $deletedSubscription->deleted_at);
        }

        if ($subscription->status !== $status && $status !== SubscriptionStatus::ACTIVE) {
            Event::assertNotDispatched(SubscriptionActivatedEvent::class);
            Event::assertNotDispatched(SubscriptionDeactivatedEvent::class);
        }
    }

    public function testItUpdatesSubscriptionStatusActiveNoTrial(): void
    {
        Event::fake([
            SubscriptionActivatedEvent::class,
            SubscriptionDeactivatedEvent::class,
        ]);

        $subscription = Subscription::withoutEvents(function () {
            return Subscription::factory()->withShopifyData()->create([
                'store_id' => $this->currentStore->id,
                'trial_days' => 0,
            ]);
        });
        $subscriptionValue = SubscriptionValue::from($subscription);

        Http::fake([
            App::context()->store->domain . '/admin/api/*/graphql.json' => Http::sequence()
                ->push(static::getShopifyQueryAppSubscriptionCurrentPeriodEndSuccessResponse()),
        ]);

        $updatedAt = Date::now();

        $subscriptionUpdatedValue = $this->subscriptionService->updateStatus(
            $subscriptionValue,
            SubscriptionStatus::ACTIVE,
            $updatedAt
        );

        $this->assertEquals($subscription->id, $subscriptionUpdatedValue->id);
        $this->assertEquals(SubscriptionStatus::ACTIVE, $subscriptionUpdatedValue->status);

        $this->assertTrue($updatedAt->isSameSecond($subscriptionUpdatedValue->activatedAt));
        $this->assertEquals(new CarbonImmutable('2024-02-19T17:00:00Z'), $subscriptionUpdatedValue->currentPeriodEnd);
        $this->assertNull($subscriptionUpdatedValue->trialPeriodEnd);

        Event::assertDispatched(
            SubscriptionActivatedEvent::class,
            function (SubscriptionActivatedEvent $event) use ($subscriptionUpdatedValue) {
                $this->assertEquals($subscriptionUpdatedValue->id, $event->subscription->id);
                $this->assertEquals($subscriptionUpdatedValue->status, $event->subscription->status);

                return true;
            },
        );
        Event::assertNotDispatched(SubscriptionDeactivatedEvent::class);

        Http::assertSent(function (Request $request) use ($subscriptionValue) {
            $body = json_decode($request->body(), true);
            $this->assertEquals($subscriptionValue->shopifyAppSubscriptionId, $body['variables']['id']);

            return $request->method() === 'POST' &&
                $request->hasHeader('X-Shopify-Access-Token', App::context()->store->accessToken);
        });
    }

    public static function subscriptionStatusProvider(): array
    {
        return [
            SubscriptionStatus::ACTIVE->value => [SubscriptionStatus::ACTIVE],
            SubscriptionStatus::CANCELLED->value => [SubscriptionStatus::CANCELLED],
            SubscriptionStatus::DECLINED->value => [SubscriptionStatus::DECLINED],
            SubscriptionStatus::EXPIRED->value => [SubscriptionStatus::EXPIRED],
            SubscriptionStatus::FROZEN->value => [SubscriptionStatus::FROZEN],
            SubscriptionStatus::PENDING->value => [SubscriptionStatus::PENDING],
        ];
    }

    public function testItDoesNotCancelActiveSubscriptionByStoreIdOnNoActiveSubscriptions(): void
    {
        Event::fake([
            SubscriptionActivatedEvent::class,
            SubscriptionDeactivatedEvent::class,
        ]);

        Subscription::withoutEvents(function () {
            return Subscription::factory()->withShopifyData()->pending()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });

        $cancelledSubscriptions = $this->subscriptionService->cancelActiveSubscriptions();

        $this->assertEquals(0, $cancelledSubscriptions->count());

        Event::assertNotDispatched(SubscriptionActivatedEvent::class);
        Event::assertNotDispatched(SubscriptionDeactivatedEvent::class);
    }

    public function testItCancelsActiveSubscriptionByStoreId(): void
    {
        Date::setTestNow('2022-02-20 00:00:00');

        Event::fake([
            SubscriptionActivatedEvent::class,
            SubscriptionDeactivatedEvent::class,
        ]);

        $subscription = Subscription::withoutEvents(function () {
            return Subscription::factory()->withShopifyData()->active()->create([
                'store_id' => $this->currentStore->id,
            ]);
        });

        $cancelledSubscriptions = $this->subscriptionService->cancelActiveSubscriptions();

        $this->assertEquals(1, $cancelledSubscriptions->count());
        $cancelledSubscription = $cancelledSubscriptions->first();

        $this->assertEquals($subscription->id, $cancelledSubscription->id);
        $this->assertEquals(SubscriptionStatus::CANCELLED, $cancelledSubscription->status);
        $this->assertEquals(Date::now(), $cancelledSubscription->deactivatedAt);
        $this->assertSoftDeleted('billings_subscriptions', ['id' => $cancelledSubscription->id]);

        Event::assertDispatched(
            SubscriptionDeactivatedEvent::class,
            function (SubscriptionDeactivatedEvent $event) use ($cancelledSubscription) {
                $this->assertEquals($cancelledSubscription->id, $event->subscription->id);
                $this->assertEquals($cancelledSubscription->status, $event->subscription->status);

                return true;
            },
        );
        Event::assertNotDispatched(SubscriptionActivatedEvent::class);
    }

    public function testItCancelsActiveSubscriptionsByStoreId(): void
    {
        Date::setTestNow('2022-02-20 00:00:00');

        Event::fake([
            SubscriptionActivatedEvent::class,
            SubscriptionDeactivatedEvent::class,
        ]);

        $ignoredSubscriptions = Subscription::withoutEvents(function () {
            return Subscription::factory()->withShopifyData()->expired()->count(2)->create([
                'store_id' => $this->currentStore->id,
            ]);
        });
        $ignoredSubscriptionIds = $ignoredSubscriptions->pluck('id')->toArray();

        $subscriptions = Subscription::withoutEvents(function () {
            return Subscription::factory()->withShopifyData()->active()->count(3)->create([
                'store_id' => $this->currentStore->id,
            ]);
        });
        $subscriptionIds = $subscriptions->pluck('id')->toArray();

        $cancelledSubscriptions = $this->subscriptionService->cancelActiveSubscriptions();

        $this->assertEquals($subscriptions->count(), $cancelledSubscriptions->count());
        foreach ($cancelledSubscriptions as $cancelledSubscription) {
            $this->assertContains($cancelledSubscription->id, $subscriptionIds);
            $this->assertNotContains($cancelledSubscription->id, $ignoredSubscriptionIds);

            $this->assertEquals(SubscriptionStatus::CANCELLED, $cancelledSubscription->status);
            $this->assertEquals(Date::now(), $cancelledSubscription->deactivatedAt);
            $this->assertSoftDeleted('billings_subscriptions', ['id' => $cancelledSubscription->id]);
        }

        Event::assertDispatchedTimes(SubscriptionDeactivatedEvent::class, 3);
        Event::assertNotDispatched(SubscriptionActivatedEvent::class);
    }

    public function testItDoesNotDispatchSubscriptionDeactivatedEvent(): void
    {
        Event::fake([
            SubscriptionActivatedEvent::class,
            SubscriptionDeactivatedEvent::class,
        ]);

        $firstSubscription = Subscription::withoutEvents(function () {
            return Subscription::factory()->withShopifyData()->create(['store_id' => $this->currentStore->id, 'status' => SubscriptionStatus::ACTIVE]);
        });
        $secondSubscription = Subscription::withoutEvents(function () {
            return Subscription::factory()->withShopifyData()->create(['store_id' => $this->currentStore->id, 'status' => SubscriptionStatus::ACTIVE]);
        });
        $firstSubscriptionValue = SubscriptionValue::from($firstSubscription);
        $secondSubscriptionValue = SubscriptionValue::from($secondSubscription);
        $updatedAt = Date::now();

        $secondSubscriptionUpdatedValue = $this->subscriptionService
            ->updateStatus($secondSubscriptionValue, SubscriptionStatus::CANCELLED, $updatedAt);

        $this->assertEquals($secondSubscriptionUpdatedValue->status, SubscriptionStatus::CANCELLED);
        $this->assertNotEquals($firstSubscriptionValue->id, $secondSubscriptionUpdatedValue->id);

        Event::assertNotDispatched(SubscriptionDeactivatedEvent::class);
        Event::assertNotDispatched(SubscriptionActivatedEvent::class);
    }
}
