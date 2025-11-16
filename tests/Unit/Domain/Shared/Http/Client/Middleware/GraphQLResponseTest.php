<?php
declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\Http\Client\Middleware;

use App\Domain\Orders\GraphQL\Shopify\Stable\Operations\RefundCreate;
use App\Domain\Shared\Exceptions\Shopify\ShopifyMutationClientException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyMutationServerException;
use Http;
use Tests\TestCase;

class GraphQLResponseTest extends TestCase
{
    public function testItSkipsNonJsonResponses(): void
    {
        Http::fake([
            '*' => Http::response(['data' => ['refundCreate' => ['userErrors' => [['field' => 'amount', 'message' => 'Amount is too high']]]]], headers: ['content-type' => 'text/plain; charset=utf-8']),
        ]);

        Http::post('https://example.myshopify.com/graphql.json', [
            'query' => RefundCreate::document(),
        ]);

        Http::assertSentCount(1);
    }

    public function testItSkipsNonUserErrorResponses(): void
    {
        Http::fake([
            '*' => Http::sequence([
                Http::response(['data' => ['refundCreate' => ['userErrors' => []]]], headers: ['content-type' => 'application/json; charset=utf-8']),
                Http::response(['data' => ['refundCreate' => ['userErrors' => null]]], headers: ['content-type' => 'application/json; charset=utf-8']),
                Http::response(['data' => ['refundCreate' => []]], headers: ['content-type' => 'application/json; charset=utf-8']),
            ]),
        ]);

        Http::post('https://example.myshopify.com/graphql.json', [
            'query' => RefundCreate::document(),
        ]);

        Http::post('https://example.myshopify.com/graphql.json', [
            'query' => RefundCreate::document(),
        ]);

        Http::post('https://example.myshopify.com/graphql.json', [
            'query' => RefundCreate::document(),
        ]);

        Http::assertSentCount(3);
    }

    public function testItSkipsNonErrorResponses(): void
    {
        Http::fake([
            '*' => Http::sequence([
                Http::response(['data' => ['refundCreate' => []]], headers: ['content-type' => 'application/json; charset=utf-8']),
                Http::response(['data' => ['refundCreate' => []], 'errors' => null], headers: ['content-type' => 'application/json; charset=utf-8']),
                Http::response(['data' => ['refundCreate' => []], 'errors' => []], headers: ['content-type' => 'application/json; charset=utf-8']),
            ]),
        ]);

        Http::post('https://example.myshopify.com/graphql.json', [
            'query' => RefundCreate::document(),
        ]);

        Http::post('https://example.myshopify.com/graphql.json', [
            'query' => RefundCreate::document(),
        ]);

        Http::post('https://example.myshopify.com/graphql.json', [
            'query' => RefundCreate::document(),
        ]);

        Http::assertSentCount(3);
    }

    public function testItReportsUserErrors(): void
    {
        Http::fake([
            '*' => Http::response(['data' => ['__typename' => 'refundCreate', 'refundCreate' => ['userErrors' => [['field' => 'amount', 'message' => 'Amount is too high']]]]], headers: ['content-type' => 'application/json; charset=utf-8']),
        ]);

        $this->expectException(ShopifyMutationClientException::class);
        $this->expectExceptionMessage('Shopify refundCreate: amount is too high');

        Http::post('https://example.myshopify.com/graphql.json', [
            'query' => RefundCreate::document(),
        ]);
    }

    public function testItReportsErrors(): void
    {
        Http::fake([
            '*' => Http::response(['data' => ['refundCreate' => []], 'errors' => [['path' => ['refundCreate', 'test'], 'message' => 'An error occurred']]], headers: ['content-type' => 'application/json; charset=utf-8']),
        ]);

        $this->expectException(ShopifyMutationServerException::class);
        $this->expectExceptionMessage('Shopify refundCreate->test: An error occurred');

        Http::post('https://example.myshopify.com/graphql.json', [
            'query' => RefundCreate::document(),
        ]);
    }
}
