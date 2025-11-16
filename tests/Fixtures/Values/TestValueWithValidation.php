<?php
declare(strict_types=1);

namespace Tests\Fixtures\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;

class TestValueWithValidation extends Value
{
    use HasValueFactory;

    public function __construct(
        public string $name,
    ) {
    }

    public static function rules()
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:4'],
        ];
    }
}
