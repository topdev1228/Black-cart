<?php
declare(strict_types=1);

namespace App\Domain\Shared\Exceptions;

use App\Exceptions\ServerApiException;
use Throwable;

class UnknownPubSubTopicException extends ServerApiException
{
    public function __construct(string $topicName, Throwable $previous = null)
    {
        parent::__construct(sprintf('Unknown topic: %s', $topicName), previous: $previous);
    }
}
