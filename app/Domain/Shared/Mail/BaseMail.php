<?php
declare(strict_types=1);

namespace App\Domain\Shared\Mail;

use Arr;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Http;

class BaseMail extends Mailable
{
    protected string $storeEmail;

    public function __construct()
    {
        $returnResponse = Http::get('http://localhost:8080/api/stores/settings');
        $this->storeEmail = Arr::get($returnResponse, 'settings.customerSupportEmail.value', '');
    }
}
