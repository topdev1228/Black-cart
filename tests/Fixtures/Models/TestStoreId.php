<?php
declare(strict_types=1);

namespace Tests\Fixtures\Models;

use App\Domain\Shared\Traits\CurrentStore;
use App\Domain\Shared\Traits\HasModelFactory;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestStoreId extends Model
{
    use HasUuids;
    use HasTimestamps;
    use HasModelFactory;
    use SoftDeletes;
    use CurrentStore;
}
