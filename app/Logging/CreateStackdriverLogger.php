<?php
declare(strict_types=1);

namespace App\Logging;

use Google\Cloud\Logging\LoggingClient;
use Monolog\Handler\PsrHandler;
use Monolog\Logger;

class CreateStackdriverLogger
{
    /**
     * Create a custom Monolog instance.
     *
     * @return Logger
     */
    public function __invoke(array $config)
    {
        $logName = $config['logName'] ?? 'shopify';
        $psrLogger = LoggingClient::psrBatchLogger($logName);
        $handler = resolve(PsrHandler::class, ['logger' => $psrLogger]);

        return resolve(Logger::class, ['name' => $logName, 'handlers' => [$handler]]);
    }
}
