<?php
declare(strict_types=1);

namespace Tests\Fixtures\Models;

use App\Domain\Shared\Traits\HasModelCollection;
use App\Domain\Shared\Traits\HasModelFactory;
use Illuminate\Database\Eloquent\Model;

class TestModelWithCollection extends Model
{
    use HasModelFactory;
    use HasModelCollection;

    protected $table = 'migrations';

    protected $fillable = [
        'name',
    ];
}
