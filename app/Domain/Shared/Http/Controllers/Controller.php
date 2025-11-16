<?php
declare(strict_types=1);

namespace App\Domain\Shared\Http\Controllers;

use App\Domain\Shared\Values\Value;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use function request;
use Spatie\LaravelData\Data;
use Stringable;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * @param int<100,599> $status
     */
    public function sendResponse(array|Arrayable|Stringable|Collection|Model|Value|Data|string|View|null|JsonResource|ResourceCollection $content = '', int $status = Response::HTTP_OK, array $headers = []): Response|JsonResponse
    {
        if ($content instanceof Responsable) {
            /** @psalm-suppress PossiblyUndefinedMethod */
            return $content->toResponse(request())->setStatusCode($status)->withHeaders($headers);
        }

        /**
         * @psalm-suppress InvalidArgument
         * @psalm-suppress ImplicitToStringCast
         */
        return response($content, $status, $headers);
    }
}
