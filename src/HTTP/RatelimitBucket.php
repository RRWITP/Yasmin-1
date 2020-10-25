<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved.
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 */

namespace CharlotteDunois\Yasmin\HTTP;

use CharlotteDunois\Yasmin\Interfaces\RatelimitBucketInterface;

/**
 * Manages a route's ratelimit in memory.
 *
 * @internal
 */
class RatelimitBucket implements RatelimitBucketInterface
{
    /**
     * The API manager.
     *
     * @var APIManager
     */
    protected $api;

    /**
     * The endpoint.
     *
     * @var string
     */
    protected $endpoint;

    /**
     * The requests limit.
     *
     * @var int
     */
    protected $limit = 0;

    /**
     * How many requests can be made.
     *
     * @var int
     */
    protected $remaining = \INF;

    /**
     * When the ratelimit gets reset.
     *
     * @var float
     */
    protected $resetTime = 0.0;

    /**
     * The request queue.
     *
     * @var APIRequest[]
     */
    protected $queue = [];

    /**
     * Whether the bucket is busy.
     *
     * @var bool
     */
    protected $busy = false;

    /**
     * DO NOT initialize this class yourself.
     *
     * @param  APIManager  $api
     * @param  string  $endpoint
     */
    public function __construct(APIManager $api, string $endpoint)
    {
        $this->api = $api;
        $this->endpoint = $endpoint;
    }

    /**
     * Destroys the bucket.
     */
    public function __destruct()
    {
        $this->clear();
    }

    /**
     * Whether we are busy.
     *
     * @return bool
     */
    public function isBusy(): bool
    {
        return $this->busy;
    }

    /**
     * Sets the busy flag (marking as running).
     *
     * @param  bool  $busy
     *
     * @return void
     */
    public function setBusy(bool $busy): void
    {
        $this->busy = $busy;
    }

    /**
     * Sets the ratelimits from the response.
     *
     * @param  int|null  $limit
     * @param  int|null  $remaining
     * @param  float|null  $resetTime  Reset time in seconds with milliseconds.
     *
     * @return \React\Promise\ExtendedPromiseInterface|void
     */
    public function handleRatelimit(?int $limit, ?int $remaining, ?float $resetTime)
    {
        if ($limit === null && $remaining === null && $resetTime === null) {
            $this->remaining++; // there is no ratelimit...

            return;
        }

        $this->limit = $limit ?? $this->limit;
        $this->remaining = $remaining ?? $this->remaining;
        $this->resetTime = $resetTime ?? $this->resetTime;

        if ($this->remaining === 0 && $this->resetTime > microtime(true)) {
            $this->api->client->emit(
                'debug',
                'Endpoint "'.$this->endpoint.'" ratelimit encountered, continueing in '.($this->resetTime - \microtime(
                        true
                    )).' seconds'
            );
        }
    }

    /**
     * Returns the endpoint this bucket is for.
     *
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Returns the size of the queue.
     *
     * @return int
     */
    public function size(): int
    {
        return count($this->queue);
    }

    /**
     * Pushes a new request into the queue.
     *
     * @param  APIRequest  $request
     *
     * @return $this
     */
    public function push(APIRequest $request)
    {
        $this->queue[] = $request;

        return $this;
    }

    /**
     * Unshifts a new request into the queue. Modifies remaining ratelimit.
     *
     * @param  APIRequest  $request
     *
     * @return $this
     */
    public function unshift(APIRequest $request)
    {
        array_unshift($this->queue, $request);
        $this->remaining++;

        return $this;
    }

    /**
     * Retrieves ratelimit meta data.
     *
     * The resolved value must be:
     * ```
     * array(
     *     'limited' => bool,
     *     'resetTime' => int|null
     * )
     * ```
     *
     * @return \React\Promise\ExtendedPromiseInterface|array
     */
    public function getMeta()
    {
        if ($this->resetTime && microtime(true) > $this->resetTime) {
            $this->resetTime = null;
            $this->remaining = ($this->limit ? $this->limit : \INF);

            $limited = false;
        } else {
            $limited = ($this->limit !== 0 && $this->remaining === 0);
        }

        return ['limited' => $limited, 'resetTime' => $this->resetTime];
    }

    /**
     * Returns the first queue item or false. Modifies remaining ratelimit.
     *
     * @return APIRequest|false
     */
    public function shift()
    {
        if (count($this->queue) === 0) {
            return false;
        }

        $item = array_shift($this->queue);
        $this->remaining--;

        return $item;
    }

    /**
     * Unsets all queue items.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->remaining = 0;
        while ($item = array_shift($this->queue)) {
            unset($item);
        }
    }
}
