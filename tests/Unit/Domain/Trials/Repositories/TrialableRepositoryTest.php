<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Trials\Repositories;

use App\Domain\Trials\Enums\TrialStatus;
use App\Domain\Trials\Models\Trialable;
use App\Domain\Trials\Repositories\TrialableRepository;
use App\Domain\Trials\Values\Trialable as TrialableValue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Str;
use Tests\TestCase;

class TrialableRepositoryTest extends TestCase
{
    protected $trialableRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trialableRepository = resolve(TrialableRepository::class);
    }

    /**
     * A basic feature test example.
     */
    public function testAllReturnsEmpty(): void
    {
        $all = $this->trialableRepository->all();

        $this->assertEquals(new Collection(), $all);
    }

    public function testAllReturnsCollection(): void
    {
        $all = $this->trialableRepository->all();

        $this->assertEquals(new Collection(), $all);
    }

    public function testGetAllIgnoresCancelledItemsByDefault(): void
    {
        $groupId = Str::uuid();
        $trial = Trialable::factory()->create([
            'status' => TrialStatus::PRETRIAL,
            'group_key' => $groupId,
        ]);

        $cancelledTrial = Trialable::factory()->create([
            'status' => TrialStatus::CANCELLED,
            'group_key' => $groupId,
        ]);

        $collection = $this->trialableRepository->getAll([
            'group_key' => $groupId,
        ]);

        $this->assertEquals(1, $collection->count());
        $this->assertEquals($trial->id, $collection->first()->id);
    }

    public function testGetAllReturnsCancelledWithFlag(): void
    {
        $groupId = Str::uuid();
        $trial = Trialable::factory()->create([
            'status' => TrialStatus::PRETRIAL,
            'group_key' => $groupId,
        ]);

        $cancelledTrial = Trialable::factory()->create([
            'status' => TrialStatus::CANCELLED,
            'group_key' => $groupId,
        ]);

        $collection = $this->trialableRepository->getAll([
            'group_key' => $groupId,
        ], true); //withCancelled

        $this->assertEquals(2, $collection->count()); //returns both
        $this->assertEquals($trial->id, $collection->first()->id);
    }

    public function testSaveNew(): void
    {
        $value = TrialableValue::builder()->create();
        $this->trialableRepository->save($value);

        $this->assertDatabaseCount('trialables', 1);
    }

    public function testSaveExisting(): void
    {
        $trialable = Trialable::factory()->create();
        $trialable->save();

        $value = TrialableValue::builder()->withId($trialable->id)->create();

        $this->trialableRepository->save($value);
        $this->assertDatabaseHas('trialables', $value->toArray());
    }

    public function testSaveExistingNotFound(): void
    {
        $id = '1234';

        $value = TrialableValue::builder()->withId($id)->create();

        $this->expectException(ModelNotFoundException::class);
        $this->trialableRepository->save($value);
    }

    public function testGetOneWithId(): void
    {
        $trialable = Trialable::factory()->create();
        $trialable->save();

        $ret = $this->trialableRepository->getById($trialable->id);
        $this->assertEquals(TrialableValue::from($trialable), $ret);
    }

    public function testGetOneWithIdDoesntExist(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->trialableRepository->getById('1234');
    }

    public function testGetOneWithKey(): void
    {
        $trialable = Trialable::factory()->create();

        $ret = $this->trialableRepository->getBySource($trialable->source_id, $trialable->source_key);
        $this->assertEquals(TrialableValue::from($trialable), $ret);
    }

    public function getGetOneWithKeyDoesntExist(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->trialableRepository->getBySource('1234', 'b4s');
    }
}
