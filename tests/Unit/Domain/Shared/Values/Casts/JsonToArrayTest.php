<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Values\Casts;

use App\Domain\Shared\Values\Casts\JsonToArray;
use InvalidArgumentException;
use JsonException;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Tests\TestCase;

class JsonToArrayTest extends TestCase
{
    public function testItDoesNotCastJsonToArrayWithInvalidInputs(): void
    {
        $jsonToArray = new JsonToArray();

        $property = $this->createMock(DataProperty::class);

        foreach ([1, 1.1, false, null] as $value) {
            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessage('Value must be an array or a string');

            $jsonToArray->cast($property, $value, [], $this->mock(CreationContext::class));
        }
    }

    public function testItDoesNotCastJsonToArrayOnMalformedInput(): void
    {
        $jsonToArray = new JsonToArray();

        $property = $this->createMock(DataProperty::class);

        $value = '{"id": "pi_3OfW7nS3TWJ79DWP15QXQfLa",';

        $this->expectException(JsonException::class);
        $this->expectExceptionMessage('Syntax error');

        $jsonToArray->cast($property, $value, [], $this->mock(CreationContext::class));
    }

    public function testItCastsJsonToArrayWithEmptyStringInput(): void
    {
        $jsonToArray = new JsonToArray();

        $property = $this->createMock(DataProperty::class);

        $value = '';
        $result = $jsonToArray->cast($property, $value, [], $this->mock(CreationContext::class));

        $this->assertEquals([], $result);
    }

    public function testItCastsJsonToArrayWithArrayInput(): void
    {
        $jsonToArray = new JsonToArray();

        $property = $this->createMock(DataProperty::class);

        $value = [
            'id' => 'pi_3OfW7nS3TWJ79DWP15QXQfLa',
            'object' => 'payment_intent',
            'charges' => [
                'object' => 'list',
                'data' => [
                    [
                        'id' => 'ch_3OfW7nS3TWJ79DWP1Aa0u2OR',
                        'object' => 'charge',
                        'amount' => 330128,
                        'application_fee' => 'fee_1OfW7nS3TWJ79DWPGMeTTF5r',
                        'balance_transaction' => [
                            'id' => 'txn_3OfW7nS3TWJ79DWP164MJSkz',
                            'object' => 'balance_transaction',
                            'exchange_rate' => 1.33747,
                        ],
                    ],
                ],
                'has_more' => false,
                'total_count' => 1,
            ],
        ];
        $result = $jsonToArray->cast($property, $value, [], $this->mock(CreationContext::class));

        $this->assertEquals([
            'id' => 'pi_3OfW7nS3TWJ79DWP15QXQfLa',
            'object' => 'payment_intent',
            'charges' => [
                'object' => 'list',
                'data' => [
                    [
                        'id' => 'ch_3OfW7nS3TWJ79DWP1Aa0u2OR',
                        'object' => 'charge',
                        'amount' => 330128,
                        'application_fee' => 'fee_1OfW7nS3TWJ79DWPGMeTTF5r',
                        'balance_transaction' => [
                            'id' => 'txn_3OfW7nS3TWJ79DWP164MJSkz',
                            'object' => 'balance_transaction',
                            'exchange_rate' => 1.33747,
                        ],
                    ],
                ],
                'has_more' => false,
                'total_count' => 1,
            ],
        ], $result);
    }

    public function testItCastsJsonToArray(): void
    {
        $jsonToArray = new JsonToArray();

        $property = $this->createMock(DataProperty::class);

        $value = '
        {
            "id": "pi_3OfW7nS3TWJ79DWP15QXQfLa",
            "object": "payment_intent",
            "charges": {
              "object": "list",
              "data": [
                {
                  "id": "ch_3OfW7nS3TWJ79DWP1Aa0u2OR",
                  "object": "charge",
                  "amount": 330128,
                  "application_fee": "fee_1OfW7nS3TWJ79DWPGMeTTF5r",
                  "balance_transaction": {
                    "id": "txn_3OfW7nS3TWJ79DWP164MJSkz",
                    "object": "balance_transaction",
                    "exchange_rate": 1.33747
                  }
                }
              ],
              "has_more": false,
              "total_count": 1
            }
        }';
        $result = $jsonToArray->cast($property, $value, [], $this->mock(CreationContext::class));

        $this->assertEquals([
            'id' => 'pi_3OfW7nS3TWJ79DWP15QXQfLa',
            'object' => 'payment_intent',
            'charges' => [
                'object' => 'list',
                'data' => [
                    [
                        'id' => 'ch_3OfW7nS3TWJ79DWP1Aa0u2OR',
                        'object' => 'charge',
                        'amount' => 330128,
                        'application_fee' => 'fee_1OfW7nS3TWJ79DWPGMeTTF5r',
                        'balance_transaction' => [
                            'id' => 'txn_3OfW7nS3TWJ79DWP164MJSkz',
                            'object' => 'balance_transaction',
                            'exchange_rate' => 1.33747,
                        ],
                    ],
                ],
                'has_more' => false,
                'total_count' => 1,
            ],
        ], $result);
    }
}
