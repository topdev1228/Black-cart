<?php

declare(strict_types=1);

namespace App\Domain\Shared\Values\Factories;

use App\Domain\Shared\Values\Factory;
use Illuminate\Support\Facades\Date;
use function json_encode;
use stdClass;

class PubSubMessageFactory extends Factory
{
    public function definition(): array
    {
        $object = new stdClass();
        $object->foo = 'bar';

        return [
            'messageId' => $this->faker->word(),
            'data' => json_encode([
                'event' => $object,
                'socket' => null,
            ], JSON_THROW_ON_ERROR),
            'attributes' => [
                'event' => $this->faker->word(),
                'publishedAt' => Date::now(),
                'uuid' => $this->faker->uuid(),
                'domain' => $this->faker->domainName(),
            ],
        ];
    }

    public function shopifyWebhook(): static
    {
        return $this->state([
            'data' => json_decode('{
               "id":6566731710550,
               "title":"Canadian Reclaimed wood framed burlap coffee bag",
               "body_html":"\u003cmeta charset=\"utf-8\"\u003e\n\u003cp data-mce-fragment=\"1\"\u003eBurlap coffee bag Cafes do Brasil framed in a painted, reclaimed wood with a unique distressed style.\u003c\/p\u003e\n\u003cp data-mce-fragment=\"1\"\u003e Ready to hang with its sawtooth hanging hardware.\u003c\/p\u003e\n\u003cp data-mce-fragment=\"1\"\u003eDimensions: 36\" width x 24\" height\u003c\/p\u003e\n\u003cp data-mce-fragment=\"1\"\u003eMade in Ontario, Canada\u003c\/p\u003e",
               "vendor":"Example shop",
               "product_type":"",
               "created_at":"2021-04-27T18:11:26-04:00",
               "handle":"reclaimed-wood-framed-burlap-coffee-bag",
               "updated_at":"2021-12-15T11:10:35-05:00",
               "published_at":null,
               "template_suffix":"",
               "status":"draft",
               "published_scope":"web",
               "tags":"",
               "admin_graphql_api_id":"gid:\/\/shopify\/Product\/6566731710550",
               "variants":[
                  {
                     "id":39346305826902,
                     "product_id":6566731710550,
                     "title":"Default Title",
                     "price":"0.00",
                     "sku":"",
                     "position":1,
                     "inventory_policy":"deny",
                     "compare_at_price":null,
                     "fulfillment_service":"manual",
                     "inventory_management":"shopify",
                     "option1":"Default Title",
                     "option2":null,
                     "option3":null,
                     "created_at":"2021-04-27T18:11:26-04:00",
                     "updated_at":"2021-12-15T11:10:35-05:00",
                     "taxable":true,
                     "barcode":"",
                     "grams":0,
                     "image_id":null,
                     "weight":0.0,
                     "weight_unit":"kg",
                     "inventory_item_id":41446019530838,
                     "inventory_quantity":0,
                     "old_inventory_quantity":0,
                     "requires_shipping":true,
                     "admin_graphql_api_id":"gid:\/\/shopify\/ProductVariant\/39346305826902"
                  }
               ],
               "options":[
                  {
                     "id":8444211003478,
                     "product_id":6566731710550,
                     "name":"Title",
                     "position":1,
                     "values":[
                        "Default Title"
                     ]
                  }
               ],
               "images":[
                  {
                     "id":28261801033814,
                     "product_id":6566731710550,
                     "position":1,
                     "created_at":"2021-04-27T18:11:28-04:00",
                     "updated_at":"2021-04-27T18:11:28-04:00",
                     "alt":null,
                     "width":1861,
                     "height":2590,
                     "src":"https:\/\/cdn.shopify.com\/s\/files\/1\/0283\/7286\/1014\/products\/PXL_20201228_211944111.jpg?v=1619561488",
                     "variant_ids":[],
                     "admin_graphql_api_id":"gid:\/\/shopify\/ProductImage\/28261801033814"
                  }
               ],
               "image":{
                  "id":28261801033814,
                  "product_id":6566731710550,
                  "position":1,
                  "created_at":"2021-04-27T18:11:28-04:00",
                  "updated_at":"2021-04-27T18:11:28-04:00",
                  "alt":null,
                  "width":1861,
                  "height":2590,
                  "src":"https:\/\/cdn.shopify.com\/s\/files\/1\/0283\/7286\/1014\/products\/PXL_20201228_211944111.jpg?v=1619561488",
                  "variant_ids":[],
                  "admin_graphql_api_id":"gid:\/\/shopify\/ProductImage\/28261801033814"
               }
            }', true, 512, JSON_THROW_ON_ERROR),
            'attributes' => [
                'X-Shopify-Topic' => 'orders/create',
                'X-Shopify-Hmac-Sha256' => 'XWmrwMey6OsLMeiZKwP4FppHH3cmAiiJJAweH5Jo4bM=',
                'X-Shopify-Shop-Domain' => 'example.myshopify.com',
                'X-Shopify-API-Version' => '2023-10',
                'X-Shopify-Webhook-Id' => 'b54557e4-bdd9-4b37-8a5f-bf7d70bcd043',
                'X-Shopify-Triggered-At' => '2023-03-29T18:00:27.877041743Z',
            ],
        ]);
    }

    public function event(string|array $event): static
    {
        return $this->state([
            'data' => !is_string($event) ? json_encode(['event' => $event], JSON_THROW_ON_ERROR) : $event,
        ]);
    }
}
