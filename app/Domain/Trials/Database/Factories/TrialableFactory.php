<?php
declare(strict_types=1);

namespace App\Domain\Trials\Database\Factories;

use App\Domain\Trials\Enums\TrialStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrialableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'source_key' => $this->faker->word(),
            'source_id' => $this->faker->uuid(),
            'status' => TrialStatus::INIT,
            'group_key' => null,
        ];
    }

    public function withId(string $id): Factory
    {
        return $this->state([
            'id' => $id,
        ]);
    }

    public function withGroupKey(string $key = null): Factory
    {
        return $this->state([
            'group_key' => $key ?: $this->faker->word(),
        ]);
    }

    public function preTrial(): Factory
    {
        return $this->state([
           'status' => 'pre-trial',
        ]);
    }

    public function trial(): Factory
    {
        return $this->state([
            'status' => 'trial',
        ]);
    }

    public function postTrial(): Factory
    {
        return $this->state([
            'status' => 'post-trial',
        ]);
    }

    public function complete(): Factory
    {
        return $this->state([
            'status' => 'complete',
        ]);
    }
}
