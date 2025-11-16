<?php
declare(strict_types=1);

namespace App\Domain\Shared\Services;

use App;
use App\Domain\Shared\Exceptions\InvalidListenerException;
use App\Domain\Shared\Facades\AppMetrics;
use App\Domain\Shared\Values\PubSubMessageEnvelope;
use App\Domain\Shared\Values\Value;
use function class_exists;
use Illuminate\Support\Collection;
use ReflectionClass;
use Str;

class PubSubDispatchService
{
    public function dispatch(PubSubMessageEnvelope $envelope): void
    {
        AppMetrics::trace('pubsub.dispatcher', function (MetricsService $metrics) use ($envelope) {
            $metrics->setTag('pubsub.message.id', $envelope->message->messageId);
            if ($envelope->message->attributes->has('uuid')) {
                $metrics->setTag('pubsub.message.uuid', $envelope->message->attributes->get('uuid'));
            }
            if ($envelope->message->attributes->has('X-Shopify-Webhook-Id')) {
                $metrics->setTag('pubsub.message.webhook_id', $envelope->message->attributes->get('X-Shopify-Webhook-Id'));
            }

            $listenerClass = $this->getListenerClass($envelope);

            /** @var class-string $listenerClass */
            if (!class_exists($listenerClass) && !App::has($listenerClass)) {
                throw new InvalidListenerException($listenerClass);
            }

            if (isset($envelope->message->data['__event'])) {
                $metrics->setTag('pubsub.event', $envelope->message->data['__event']);
            }
            $metrics->setTag('pubsub.listener.name', $listenerClass);
            $metrics->setTag('pubsub.value.size', (string) $envelope->message->data->count());

            $listener = resolve($listenerClass);
            $args = $this->initializeArguments($listener, $metrics, $envelope);

            AppMetrics::trace('pubsub.listener.handle', function () use ($listener, $args) {
                $listener->handle(...$args);
            });
        });
    }

    /**
     * Transform subscription name to a listener class name
     *
     * Subscription name Format:
     * shopify-{domain}-{listener-name}
     *
     * Example:
     * shopify-programs-create-program-for-store => \App\Domain\Programs\Listeners\CreateProgramForStoreListener
     */
    protected function getListenerClass(PubSubMessageEnvelope $envelope): string
    {
        $listenerClass = Str::of($envelope->subscription)->after('shopify-');

        return $listenerClass->after('-')->studly()->prepend('App\\Domain\\', $listenerClass->before('-')->studly()->toString(), '\\Listeners\\')->append('Listener')->toString();
    }

    protected function initializeArguments(object $listener, MetricsService $metrics, PubSubMessageEnvelope $envelope): array
    {
        $parameters = (new ReflectionClass($listener))->getMethod('handle')->getParameters();

        if (empty($parameters)) {
            return [];
        }

        $args = [];

        /** @psalm-suppress UndefinedMethod */
        $type = $parameters[0]->getType()?->getName();

        // First argument is a Value object
        if ($type !== null && is_a($type, Value::class, true)) {
            $metrics->setTag('pubsub.is_event', '1');
            $metrics->setTag('pubsub.args.type', 'value');
            $metrics->setTag('pubsub.args.class', $type);
            $args[] = $type::from($envelope->message->data);
        }

        // First argument is a Laravel Collection
        if ($type !== null && is_a($type, Collection::class, true)) {
            $metrics->setTag('pubsub.is_event', '0');
            $metrics->setTag('pubsub.args.type', 'collection');
            $args[] = $envelope->message->data;
        }

        // First argument is an array
        if ($type === 'array') {
            $metrics->setTag('pubsub.is_event', '0');
            $metrics->setTag('pubsub.args.type', 'array');
            $args[] = $envelope->message->data->toArray();
        }

        if (count($parameters) > 1) {
            $metrics->setTag('pubsub.args.attributes', '1');
            $metrics->setTag('pubsub.attributes.size', (string) $envelope->message->attributes->count());

            /** @psalm-suppress UndefinedMethod */
            $type = $parameters[1]->getType()?->getName();

            // Second argument is a Value object
            if ($type !== null && is_a($type, Value::class, true)) {
                $metrics->setTag('pubsub.args.attributes.type', 'value');
                $metrics->setTag('pubsub.args.attributes.class', (string) $type);
                $args[] = $type::from($envelope->message->attributes);
            }

            // Second argument is a Laravel Collection
            if ($type !== null && is_a($type, Collection::class, true)) {
                $metrics->setTag('pubsub.args.attributes.type', 'collection');
                $args[] = $envelope->message->attributes;
            }

            // Second argument is an array
            if ($type === 'array') {
                $metrics->setTag('pubsub.args.attributes.type', 'array');
                $args[] = $envelope->message->attributes->toArray();
            }
        }

        return $args;
    }
}
