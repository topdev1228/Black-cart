<?php

declare(strict_types=1);

namespace App\Domain\Shared\Values\Factories;

use App\Domain\Shared\Values\Factory;
use App\Domain\Shared\Values\PubSubMessage;
use Illuminate\Support\Collection;

class PubSubMessageEnvelopeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'subscription' => $this->faker->word(),
            'message' => PubSubMessage::builder()->create(),
        ];
    }

    public function shopifyWebhook(): static
    {
        /** @psalm-suppress UndefinedMethod */
        return $this->state([
            'message' => PubSubMessage::builder()->shopifyWebhook()->create(),
        ]);
    }

    public function data(array|Collection $event): static
    {
        /** @psalm-suppress InvalidPropertyAssignment */
        $this->values['message']->data = collect($event);

        return $this->state([
            'message' => $this->values['message'],
        ]);
    }

    public function attributes(array|Collection $attributes): static
    {
        /** @psalm-suppress InvalidPropertyAssignment */
        $this->values['message']->attributes = $this->values['message']->attributes->merge($attributes);

        return $this->state([
            'message' => $this->values['message'],
        ]);
    }
}
