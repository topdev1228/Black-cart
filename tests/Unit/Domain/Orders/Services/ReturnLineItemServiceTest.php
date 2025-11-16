<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Orders\Services;

use App\Domain\Orders\Repositories\ReturnLineItemRepository;
use App\Domain\Orders\Services\ReturnLineItemService;
use App\Domain\Orders\Values\ReturnLineItem as ReturnLineItemValue;
use Tests\TestCase;

class ReturnLineItemServiceTest extends TestCase
{
    protected $repositoryMock;
    protected ReturnLineItemService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = $this->mock(ReturnLineItemRepository::class);
        $this->service = resolve(ReturnLineItemService::class);
    }

    public function testSave(): void
    {
        $value = ReturnLineItemValue::builder()->create();
        $this->repositoryMock->shouldReceive('save')->andReturn($value);

        $return = $this->service->save($value);

        $this->assertEquals($value->id, $return->id);
    }
}
