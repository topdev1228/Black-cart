<?php
declare(strict_types=1);

namespace App\Domain\Orders\Models;

use App\Domain\Shared\Traits\HasModelFactory;
use Carbon\CarbonImmutable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Orders\Models\TrialGroup
 *
 * @property string $id
 * @property string $order_id
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|TrialGroup newModelQuery()
 * @method static Builder|TrialGroup newQuery()
 * @method static Builder|TrialGroup query()
 * @method static Builder|TrialGroup whereCreatedAt($value)
 * @method static Builder|TrialGroup whereDeletedAt($value)
 * @method static Builder|TrialGroup whereId($value)
 * @method static Builder|TrialGroup whereOrderId($value)
 * @method static Builder|TrialGroup whereUpdatedAt($value)
 * @mixin Eloquent
 */
class TrialGroup extends Model
{
    use HasModelFactory;
    use HasUuids;

    protected $fillable = ['order_id'];
    protected $table = 'orders_trial_group';
}
