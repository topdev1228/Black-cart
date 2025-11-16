<?php
declare(strict_types=1);

namespace App\Domain\Orders\Services;

use App\Domain\Orders\Events\LineItemCancelled;
use App\Domain\Orders\Repositories\LineItemRepository;
use App\Domain\Orders\Repositories\OrderRepository;
use App\Domain\Orders\Repositories\TrialGroupRepository;
use App\Domain\Orders\Values\LineItem;
use App\Domain\Orders\Values\Order;
use App\Domain\Orders\Values\Trialable;
use App\Domain\Orders\Values\TrialGroup;
use App\Domain\Trials\Enums\TrialStatus;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Http;

class TrialService
{
    const TRIAL_SOURCE_KEY = 'blackcart_shopify_app';
    const DEFAULT_TRIAL_DAYS = 7;

    public function __construct(
        protected OrderRepository $orderRepository,
        protected TrialGroupRepository $trialGroupRepository,
        protected LineItemRepository $lineItemRepository,
    ) {
    }

    public function initiateTrial(LineItem $lineItem, int $tryDays = self::DEFAULT_TRIAL_DAYS): void
    {
        if (!empty($lineItem->trialableId) || empty($lineItem->orderId)) {
            return;
        }

        $trialGroup = $this->trialGroup($this->orderRepository->getById($lineItem->orderId));
        $trialValue = Trialable::from([
            'source_key' => self::TRIAL_SOURCE_KEY,
            'source_id' => $lineItem->id,
            'status' => TrialStatus::INIT,
            'group_key' => $trialGroup->id,
            'trial_duration' => $tryDays,
        ]);

        $trial = $this->postTrial($trialValue);

        $lineItem->trialableId = $trial->id;
        $lineItem->trialGroupId = $trial->groupKey;

        $this->lineItemRepository->update($lineItem);
    }

    public function cancelTrial(LineItem $lineItem): void
    {
        if (empty($lineItem->trialableId)) {
            return;
        }

        LineItemCancelled::dispatch($lineItem->id);
    }

    public function trialGroup(Order $order): TrialGroup
    {
        try {
            return $this->trialGroupRepository->getByOrder($order);
        } catch (ModelNotFoundException $e) {
            return $this->trialGroupRepository->createForOrder($order);
        }
    }

    public function postTrial(Trialable $trialValue): Trialable
    {
        $trialResponse = Http::post('http://localhost:8080/api/trials', $trialValue->toArray());

        return Trialable::from($trialResponse->throwUnlessStatus(201)->json()['trial']);
    }
}
