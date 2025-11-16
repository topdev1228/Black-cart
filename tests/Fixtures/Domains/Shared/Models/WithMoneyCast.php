<?php
declare(strict_types=1);

namespace Tests\Fixtures\Domains\Shared\Models;

use App\Domain\Shared\Models\Casts\Money;
use Illuminate\Database\Eloquent\Model;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

/**
 * @property Money $amount
 */
class WithMoneyCast extends Model
{
    protected $fillable = [
        'amount',
        'amount2',
        'currency',
    ];

    protected function casts(): array
    {
        return [
            'amount' => Money::class,
            'amount2' => Money::class,
            'currency' => CurrencyAlpha3::class,
        ];
    }
}
