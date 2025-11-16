<?php
declare(strict_types=1);

namespace App\Domain\Trials\Services;

use App\Domain\Trials\Enums\TrialStatus;
use App\Domain\Trials\Events\TrialExpiredEvent;
use App\Domain\Trials\Events\TrialGroupExpiredEvent;
use App\Domain\Trials\Events\TrialGroupStartedEvent;
use App\Domain\Trials\Events\TrialStartedEvent;
use App\Domain\Trials\Repositories\TrialableRepository;
use App\Domain\Trials\Values\Collections\TrialableCollection;
use App\Domain\Trials\Values\Program;
use App\Domain\Trials\Values\Trialable;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;

class TrialService
{
    const TRIAL_SOURCE_KEY = 'blackcart_shopify_app';

    public function __construct(protected TrialableRepository $trialableRepository)
    {
    }

    public function getTrialable(string $id): Trialable
    {
        return $this->trialableRepository->getById($id);
    }

    public function getBySource(string $sourceId, string $sourceKey = self::TRIAL_SOURCE_KEY): Trialable
    {
        return $this->trialableRepository->getBySource($sourceId, $sourceKey);
    }

    public function all(): iterable
    {
        return $this->trialableRepository->all();
    }

    public function create(Trialable $trialable): Trialable
    {
        return $this->trialableRepository->save($trialable);
    }

    public function update(Trialable $trialable, array $data): Trialable
    {
        $trialable = Trialable::from($data + $trialable->toArray());

        return $this->trialableRepository->save($trialable);
    }

    public function cancelTrial(Trialable $trialable): void
    {
        $this->update($trialable, [
            'status' => TrialStatus::CANCELLED,
        ]);

        // if this is the last item, attempt to move remaining items
        if ($this->isGroupReadyForTrial($trialable)) {
            $this->moveGroupToTrial(
                $this->getGroup($trialable)
            );
        }
    }

    /**
     * This function is symbolic until we implement the condition system
     *
     * @return void
     */
    public function updateCondition(Trialable $trialable, string $condition)
    {
        $trialable = $this->update($trialable, [
            'status' => TrialStatus::PRETRIAL,
        ]);

        if ($this->isGroupReadyForTrial($trialable)) {
            $this->moveGroupToTrial(
                $this->getGroup($trialable)
            );
        }
    }

    public function isGroupReadyForTrial(Trialable $trialable): bool
    {
        $group = $this->getGroup($trialable);
        foreach ($group->items() as $trialItem) {
            if (!$trialItem->readyForTrial()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     */
    public function getGroup(Trialable $trialable): TrialableCollection
    {
        if ($trialable->groupKey === null) {
            return Trialable::collection([$trialable]);
        }

        return $this->trialableRepository->getAll([
            'source_key' => $trialable->sourceKey,
            'group_key' => $trialable->groupKey,
        ]);
    }

    public function moveGroupToTrial(TrialableCollection $collection): void
    {
        foreach ($collection->items() as $trialItem) {
            if ($trialItem->status !== TrialStatus::PRETRIAL) {
                continue;
            }

            $this->update($trialItem, [
                'status' => TrialStatus::TRIAL,
            ]);
            TrialStartedEvent::dispatch($trialItem);
        }

        $groupKey = $collection->first()?->groupKey;
        if ($groupKey) {
            TrialGroupStartedEvent::dispatch($groupKey);
        }
    }

    public function expireTrial(Trialable $trialable): void
    {
        if (!in_array($trialable->status, [TrialStatus::TRIAL, TrialStatus::POSTTRIAL])) {
            return;
        }

        $trialable = $this->update($trialable, [
            'status' => TrialStatus::COMPLETE,
        ]);

        TrialExpiredEvent::dispatch($trialable);

        $group = $this->getGroup($trialable);
        $this->expireGroup($group);
    }

    public function expireGroup(TrialableCollection $collection): void
    {
        foreach ($collection->items() as $item) {
            if ($item->status !== TrialStatus::COMPLETE) {
                return;
            }
        }

        $groupKey = $collection->first()?->groupKey;
        if ($groupKey) {
            TrialGroupExpiredEvent::dispatch($groupKey);
        }
    }

    public function calculateExpiryTime(Trialable $trialable): CarbonImmutable
    {
        $program = $this->getProgram();

        return Date::now()->addDays($trialable->trialDuration)->addDays($program->dropOffDays);
    }

    private function getProgram(): ?Program
    {
        $response = Http::get('http://localhost:8080/api/stores/programs');

        $programData = $response['programs'][0] ?? null;

        return $programData ? Program::from($programData) : null;
    }
}
