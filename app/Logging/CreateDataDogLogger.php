<?php
declare(strict_types=1);

namespace App\Logging;

use App;
use Auth;
use function dd_trace_peek_span_id;
use function DDTrace\trace_id;
use Illuminate\Http\Request;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\BufferHandler;
use Monolog\Logger;
use Myli\DatadogLogger\Api\DataDogApiHandler;
use Myli\DatadogLogger\ApiKeyNotFoundException;

class CreateDataDogLogger
{
    protected $request;

    public function __construct(Request $request = null)
    {
        $this->request = $request;
    }

    public function __invoke(array $config)
    {
        $log = resolve(Logger::class, ['name' => 'datadog']);

        $isEuropeRegion = false;
        if (!empty($config['region']) && $config['region'] === 'eu') {
            $isEuropeRegion = true;
        }
        if (empty($config['apiKey'])) {
            throw new ApiKeyNotFoundException();
        }

        $dataDogHandler = resolve(DataDogApiHandler::class, ['token' => $config['apiKey'], 'europeRegion' => $isEuropeRegion, 'level' => $config['level'] ?? Logger::DEBUG, 'bubble' => $config['bubble'] ?? true]);

        // using the BufferHandler improves the performance by batching the log
        // messages to the end of the request
        $handler = resolve(BufferHandler::class, ['handler' => $dataDogHandler]);
        $formatter = resolve(JsonFormatter::class);
        $formatter->includeStacktraces();
        $handler->setFormatter($formatter);
        $log->pushHandler($handler);

        foreach ($log->getHandlers() as $handler) {
            $handler->pushProcessor([$this, 'includeMetaData']);
        }

        return $log;
    }

    // lets add some extra metadata to every request
    public function includeMetaData(array $record): array
    {
        // set the service or app name to the record
        $record['service'] = config('logging.channels.datadog.service');
        if (!isset($record['extra'])) {
            $record['extra'] = [];
        }
        $record['extra']['env'] = App::environment();

        // set the hostname to record so we know host this was created on
        $record['hostname'] = gethostname();

        // check to see if we have a request
        if ($this->request) {
            $record['extra'] += [
                'ip' => $this->request->getClientIp(),
            ];

            // get the authenticated user
            $user = Auth::user();

            // add the user information
            if ($user) {
                $record['user'] = [
                    'id' => $user->id ?? 'unknown',
                ];
            }
        }

        if (extension_loaded('ddtrace')) {
            $record['dd'] = [
                'trace_id' => trace_id(),
                'span_id' => dd_trace_peek_span_id(),
            ];
        }

        $record['ddsource'] = 'php';

        return $record;
    }
}
