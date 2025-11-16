<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Billings\Repositories;

use App;
use App\Domain\Billings\Enums\SubscriptionStatus;
use App\Domain\Billings\Models\Subscription;
use App\Domain\Billings\Repositories\SubscriptionRepository;
use App\Domain\Billings\Values\Subscription as SubscriptionValue;
use App\Domain\Stores\Models\Store;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SubscriptionRepositoryTest extends TestCase
{
    protected Store $currentStore;
    protected SubscriptionRepository $subscriptionRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->currentStore = Store::factory()->create();
        App::context(store: $this->currentStore);

        $this->subscriptionRepository = resolve(SubscriptionRepository::class);
    }

    public function testItDoesNotGetByShopifyAppSubscriptionIdOnNonExistentSubscription(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->subscriptionRepository->getByShopifyAppSubscriptionId('non-existent-shopify-app-subscription-id');
    }

    public function testItGetsByShopifyAppSubscriptionId(): void
    {
        $subscription = Subscription::factory()->withShopifyData()->create(['store_id' => $this->currentStore->id]);

        $actualSubscription = $this->subscriptionRepository->getByShopifyAppSubscriptionId($subscription->shopify_app_subscription_id);

        $this->assertEquals($subscription->id, $actualSubscription->id);
        $this->assertEquals($subscription->store_id, $actualSubscription->storeId);
        $this->assertEquals($subscription->shopify_app_subscription_id, $actualSubscription->shopifyAppSubscriptionId);
        $this->assertEquals($subscription->shopify_confirmation_url, $actualSubscription->shopifyConfirmationUrl);
        $this->assertEquals($subscription->status, $actualSubscription->status);
        $this->assertEquals($subscription->activated_at, $actualSubscription->activatedAt);
        $this->assertEquals($subscription->deactivated_at, $actualSubscription->deactivatedAt);
    }

    public function testItCreatesSubscription(): void
    {
        $subscriptionValue = SubscriptionValue::builder()->withShopifyData()->create([
            'store_id' => $this->currentStore->id,
        ]);

        $actualSubscription = $this->subscriptionRepository->store($subscriptionValue);

        $this->assertNotEmpty($actualSubscription->id);
        $this->assertEquals($subscriptionValue->storeId, $actualSubscription->storeId);
        $this->assertEquals($subscriptionValue->shopifyAppSubscriptionId, $actualSubscription->shopifyAppSubscriptionId);
        $this->assertEquals($subscriptionValue->shopifyConfirmationUrl, $actualSubscription->shopifyConfirmationUrl);
        $this->assertEquals($subscriptionValue->status, $actualSubscription->status);
        $this->assertEquals($subscriptionValue->activatedAt, $actualSubscription->activatedAt);
        $this->assertEquals($subscriptionValue->deactivatedAt, $actualSubscription->deactivatedAt);
    }

    #[DataProvider('updateSubscriptionRepositoryProvider')]
    public function testItUpdatesSubscription(
        SubscriptionStatus $newStatus,
        string $activatedAt,
        string $deactivatedAt,
    ): void {
        $subscription = Subscription::withoutEvents(function () {
            return Subscription::factory()->withShopifyData()->create(['store_id' => $this->currentStore->id]);
        });

        $changes = [
            'status' => $newStatus,
        ];
        if (!empty($activatedAt)) {
            $changes['activated_at'] = $activatedAt;
        }
        if (!empty($deactivatedAt)) {
            $changes['deactivated_at'] = $deactivatedAt;
        }

        $actualSubscription = $this->subscriptionRepository->update($subscription->id, $changes);

        $this->assertEquals($newStatus, $actualSubscription->status);

        if (!empty($activatedAt)) {
            $this->assertEquals($activatedAt, $actualSubscription->activatedAt);
        }
        if (!empty($deactivatedAt)) {
            $this->assertEquals($deactivatedAt, $actualSubscription->deactivatedAt);
        }
    }

    public static function updateSubscriptionRepositoryProvider(): array
    {
        return [
            'pending' => [
                SubscriptionStatus::PENDING,
                '',
                '',
            ],
            'active' => [
                SubscriptionStatus::ACTIVE,
                '2023-12-19 19:00:00',
                '',
            ],
            'cancelled' => [
                SubscriptionStatus::CANCELLED,
                '',
                '2023-12-19 19:00:00',
            ],
            'declined' => [
                SubscriptionStatus::DECLINED,
                '',
                '2023-12-19 19:00:00',
            ],
            'expired' => [
                SubscriptionStatus::EXPIRED,
                '',
                '2023-12-19 19:00:00',
            ],
            'frozen' => [
                SubscriptionStatus::FROZEN,
                '',
                '2023-12-19 19:00:00',
            ],
        ];
    }

    public function testItGetsNoSubscriptionsByStatusOnMismatchingStatuses(): void
    {
        Subscription::withoutEvents(function () {
            Subscription::factory()->withShopifyData()->cancelled()->create(['store_id' => $this->currentStore->id]);
            Subscription::factory()->withShopifyData()->declined()->create(['store_id' => $this->currentStore->id]);
            Subscription::factory()->withShopifyData()->expired()->create(['store_id' => $this->currentStore->id]);
            Subscription::factory()->withShopifyData()->frozen()->create(['store_id' => $this->currentStore->id]);
        });

        $actualSubscriptions = $this->subscriptionRepository
            ->getByStatus(SubscriptionStatus::ACTIVE);
        $this->assertEmpty($actualSubscriptions);
    }

    public function testItGetsByStatusBySingleStatus(): void
    {
        Subscription::withoutEvents(function () {
            Subscription::factory()->withShopifyData()->cancelled()->create(['store_id' => $this->currentStore->id]);
            Subscription::factory()->withShopifyData()->declined()->create(['store_id' => $this->currentStore->id]);
            Subscription::factory()->withShopifyData()->expired()->create(['store_id' => $this->currentStore->id]);
            Subscription::factory()->withShopifyData()->frozen()->create(['store_id' => $this->currentStore->id]);
            Subscription::factory()->withShopifyData()->pending()->create(['store_id' => $this->currentStore->id]);
        });

        $expectedSubscriptions = Subscription::withoutEvents(function () {
            return Subscription::factory()->withShopifyData()->count(3)->active()
                ->create(['store_id' => $this->currentStore->id]);
        });
        $expectedSubscriptionIds = $expectedSubscriptions->pluck('id')->toArray();

        $actualSubscriptions = $this->subscriptionRepository
            ->getByStatus(SubscriptionStatus::ACTIVE);
        $this->assertCount(3, $actualSubscriptions);
        foreach ($actualSubscriptions as $actualSubscription) {
            $this->assertContains($actualSubscription->id, $expectedSubscriptionIds);
        }
    }

    public function testItGetsByStoreIdAndStatusesByMultipleStatuses(): void
    {
        Subscription::withoutEvents(function () {
            return Subscription::factory()->withShopifyData()->count(3)->active()
                ->create(['store_id' => $this->currentStore->id]);
        });

        $expectedSubscriptions = Subscription::withoutEvents(function () {
            $cancelled = Subscription::factory()->withShopifyData()->cancelled()->create(['store_id' => $this->currentStore->id]);
            $declined = Subscription::factory()->withShopifyData()->declined()->create(['store_id' => $this->currentStore->id]);
            $expired = Subscription::factory()->withShopifyData()->expired()->create(['store_id' => $this->currentStore->id]);
            $frozen = Subscription::factory()->withShopifyData()->frozen()->create(['store_id' => $this->currentStore->id]);

            return [
                $cancelled->id => $cancelled,
                $declined->id => $declined,
                $expired->id => $expired,
                $frozen->id => $frozen,
            ];
        });

        $actualSubscriptions = $this->subscriptionRepository->getByStatus(
            SubscriptionStatus::CANCELLED,
            SubscriptionStatus::DECLINED,
            SubscriptionStatus::EXPIRED,
            SubscriptionStatus::FROZEN,
        );
        $this->assertCount(4, $actualSubscriptions);
        foreach ($actualSubscriptions as $actualSubscription) {
            $this->assertArrayHasKey($actualSubscription->id, $expectedSubscriptions);
        }
    }

    public function testItDoesNotDeleteNonExistentSubscription(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $actualSubscription = $this->subscriptionRepository->delete('non-existent-subscription-id');
    }

    public function testItDeletes(): void
    {
        $subscription = Subscription::factory()->withShopifyData()->create(['store_id' => $this->currentStore->id]);

        $actualSubscription = $this->subscriptionRepository->delete($subscription->id);

        $this->assertEquals($subscription->id, $actualSubscription->id);
        $this->assertSoftDeleted('billings_subscriptions', ['id' => $subscription->id]);
    }
}
