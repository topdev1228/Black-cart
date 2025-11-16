<?php
declare(strict_types=1);

namespace Tests\Fixtures\Models;

use App\Domain\Shared\Traits\HasModelFactory;
use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    use HasModelFactory;

    protected $fillable = [
        'name',
    ];
}
