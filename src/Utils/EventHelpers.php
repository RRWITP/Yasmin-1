<?php

namespace CharlotteDunois\Yasmin\Utils;

use CharlotteDunois\Collect\Collection;
use CharlotteDunois\Events\EventEmitterInterface;
use OutOfBoundsException;
use RangeException;
use React\Promise\ExtendedPromiseInterface;

/**
 * Class EventHelpers
 *
 * Event Helper methods.
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 */
class EventHelpers
{
    /**
     * Waits for a specific type of event to get emitted. Additional filter may be applied to look for a specific event (invoked as `$filter(...$args)`). Resolves with an array of arguments (from the event).
     *
     * Options may be:
     * ```
     * array(
     *     'time' => int, (if the event hasn't been found yet, this will define a timeout (in seconds) after which the promise gets rejected)
     * )
     * ```
     *
     * @param  EventEmitterInterface  $emitter
     * @param  string  $event
     * @param  callable|null  $filter
     * @param  array  $options
     *
     * @return ExtendedPromiseInterface  This promise is cancellable.
     * @throws RangeException          The exception the promise gets rejected with, if waiting times out.
     * @throws OutOfBoundsException    The exception the promise gets rejected with, if the promise gets cancelled.
     */
    public static function waitForEvent($emitter, string $event, ?callable $filter = null, array $options = [])
    {
        $options['max'] = 1;
        $options['time'] = $options['time'] ?? 0;
        $options['errors'] = ['max'];

        $collector = new Collector(
            $emitter,
            $event,
            function (...$a) {
                return [0, $a];
            },
            $filter,
            $options
        );

        return $collector->collect()->then(
            function (Collection $bucket) {
                return $bucket->first();
            }
        );
    }
}
