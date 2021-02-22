<?php

namespace CharlotteDunois\Yasmin\WebSocket;

use CharlotteDunois\Events\EventEmitterInterface;
use CharlotteDunois\Events\EventEmitterTrait;
use CharlotteDunois\Yasmin\Client;
use CharlotteDunois\Yasmin\Interfaces\WSEncodingInterface;
use Exception;
use LogicException;
use Ratchet\Client\Connector;
use React\Promise\ExtendedPromiseInterface;
use React\Promise\Promise;
use RuntimeException;
use Throwable;

/**
 * Class WSManager
 *
 * Manages the WS connections.
 *
 * @property Client              $client
 * @property Connector           $connector
 * @property WSEncodingInterface $encoding
 * @property string              $gateway
 * @property int                 $lastIdentify
 * @property WSHandler           $wshandler
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 * @internal
 */
class WSManager implements EventEmitterInterface
{
    use EventEmitterTrait;

    /**
     * WS OP codes.
     *
     * @var array
     * @internal
     */
    const OPCODES = [
        'DISPATCH'              => 0,
        'HEARTBEAT'             => 1,
        'IDENTIFY'              => 2,
        'STATUS_UPDATE'         => 3,
        'VOICE_STATE_UPDATE'    => 4,
        'RESUME'                => 6,
        'RECONNECT'             => 7,
        'REQUEST_GUILD_MEMBERS' => 8,
        'INVALID_SESSION'       => 9,
        'HELLO'                 => 10,
        'HEARTBEAT_ACK'         => 11,

        0  => 'DISPATCH',
        1  => 'HEARTBEAT',
        2  => 'IDENTIFY',
        3  => 'STATUS_UPDATE',
        4  => 'VOICE_STATE_UPDATE',
        6  => 'RESUME',
        7  => 'RECONNECT',
        8  => 'REQUEST_GUILD_MEMBERS',
        9  => 'INVALID_SESSION',
        10 => 'HELLO',
        11 => 'HEARTBEAT_ACK',
    ];

    /**
     * WS constants. Query string parameters.
     *
     * @var array
     * @internal
     */
    const WS = [
        'v'        => 6,
        'encoding' => 'json',
    ];

    /**
     * WS Close codes.
     *
     * @var array
     * @internal
     */
    const WS_CLOSE_CODES = [
        4004 => 'Tried to identify with an invalid token',
        4010 => 'Sharding data provided was invalid',
        4011 => 'Shard would be on too many guilds if connected',
        4012 => 'Invalid gateway version',
    ];

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Connector
     */
    protected $connector;

    /**
     * @var WSHandler
     */
    protected $wshandler;

    /**
     * @var WSConnection[]
     */
    protected $connections = [];

    /**
     * @var int
     */
    protected $readyConns = 0;

    /**
     * @var string
     */
    protected $compression;

    /**
     * @var WSEncodingInterface
     */
    protected $encoding;

    /**
     * The WS gateway address.
     *
     * @var string
     */
    protected $gateway;

    /**
     * The WS gateway query string.
     *
     * @var array
     */
    protected $gatewayQS = [];

    /**
     * The timestamp of the latest identify (Ratelimit 1/5s).
     *
     * @var int
     */
    protected $lastIdentify;

    /**
     * DO NOT initialize this class yourself.
     *
     * @param  Client  $client
     *
     * @throws RuntimeException
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->wshandler = new WSHandler($this);

        $compression = $this->client->getOption('ws.compression', Client::WS_DEFAULT_COMPRESSION);

        $name = str_replace('-', '', ucwords($compression, '-'));
        if (strpos($name, '\\') === false) {
            $name = '\\CharlotteDunois\\Yasmin\\WebSocket\\Compression\\'.$name;
        }

        if (! class_exists($name, true)) {
            throw new RuntimeException('Specified WS compression class does not exist');
        }

        $name::supported();

        $interfaces = class_implements($name);
        if (! in_array('CharlotteDunois\\Yasmin\\Interfaces\\WSCompressionInterface', $interfaces)) {
            throw new RuntimeException('Specified WS compression class does not implement necessary interface');
        }

        $this->compression = $name;

        if (! $this->connector) {
            $this->connector = new Connector($this->client->loop);
        }

        $listener = function () {
            $this->readyConns++;

            if ($this->readyConns >= $this->client->getOption('numShards')) {
                $this->emit('ready');
            }
        };

        $this->on('self.ws.ready', $listener);
        $this->once(
            'ready',
            function () use (&$listener) {
                $this->removeListener('self.ws.ready', $listener);
            }
        );
    }

    /**
     * @return bool
     * @throws Exception
     * @internal
     */
    public function __isset($name)
    {
        try {
            return $this->$name !== null;
        } catch (RuntimeException $e) {
            if ($e->getTrace()[0]['function'] === '__get') {
                return false;
            }

            throw $e;
        }
    }

    /**
     * @return mixed
     * @throws RuntimeException
     */
    public function __get($name)
    {
        switch ($name) {
            case 'client':
                return $this->client;
                break;
            case 'connector':
                return $this->connector;
                break;
            case 'encoding':
                return $this->encoding;
                break;
            case 'gateway':
                return $this->gateway;
                break;
            case 'gatewayQS':
                return $this->gatewayQS;
                break;
            case 'lastIdentify':
                return $this->lastIdentify;
                break;
            case 'wshandler':
                return $this->wshandler;
                break;
        }

        throw new RuntimeException('Undefined property: '.get_class($this).'::$'.$name);
    }

    /**
     * Disconnects.
     *
     * @return void
     */
    public function destroy()
    {
        foreach ($this->connections as $ws) {
            $ws->disconnect();
        }
    }

    /**
     * Connects the specified shard to the gateway url. Resolves with an instance of WSConnection.
     *
     * @param  int  $shardID
     * @param  string|null  $gateway
     * @param  array  $querystring
     *
     * @return ExtendedPromiseInterface
     * @throws Throwable
     * @see \CharlotteDunois\Yasmin\WebSocket\WSConnection
     */
    public function connectShard(int $shardID, ?string $gateway = null, array $querystring = [])
    {
        if (! $gateway && ! $this->gateway) {
            throw new RuntimeException('Unable to connect to unknown gateway for shard '.$shardID);
        }

        if (empty($this->client->token)) {
            throw new LogicException('No client token to start with');
        }

        if (($this->lastIdentify ?? 0) > (time() - 5)) {
            return new Promise(
                function (callable $resolve, callable $reject) use ($shardID, $gateway, $querystring) {
                    $this->client->addTimer(
                        (5 - (time() - $this->lastIdentify)),
                        function () use ($shardID, $gateway, $querystring, $resolve, $reject) {
                            $this->connectShard($shardID, $gateway, $querystring)->done($resolve, $reject);
                        }
                    );
                }
            );
        }

        $reconnect = false;
        if ($this->gateway && (! $gateway || $this->gateway === $gateway)) {
            if (! $gateway) {
                $gateway = $this->gateway;
            }

            if (($this->lastIdentify ?? 0) > (time(
                    ) - 30)) { // Make sure we reconnect after at least 30 seconds, if there was like an outage, to prevent spamming
                return new Promise(
                    function (callable $resolve, callable $reject) use ($shardID, $gateway, $querystring) {
                        $time = (30 - (time() - $this->lastIdentify));
                        $this->client->emit(
                            'debug',
                            'Reconnect for shard '.$shardID.' will be attempted in '.$time.' seconds'
                        );

                        $this->client->addTimer(
                            $time,
                            function () use ($shardID, $gateway, $querystring, $resolve, $reject) {
                                $this->connectShard($shardID, $gateway, $querystring)->done($resolve, $reject);
                            }
                        );
                    }
                );
            }

            $shard = $this->client->shards->get($shardID);
            if ($shard !== null) {
                $this->client->emit('reconnect', $shard);
            }

            $reconnect = true;
        }

        $this->handleConnectEncoding($querystring);
        $this->createConnection($shardID);
        $this->gateway = $this->handleConnectGateway($gateway, $querystring);

        return $this->connections[$shardID]->connect($reconnect);
    }

    /**
     * Set last identified timestamp.
     *
     * @param  int  $lastIdentified
     *
     * @return void
     */
    public function setLastIdentified(int $lastIdentified)
    {
        $this->lastIdentify = $lastIdentified;
    }

    /**
     * Creates a new ws connection for a specific shard, if necessary.
     *
     * @param  int  $shardID
     *
     * @return void
     */
    protected function createConnection(int $shardID)
    {
        if (empty($this->connections[$shardID])) {
            $this->connections[$shardID] = new WSConnection($this, $shardID, $this->compression);

            $this->connections[$shardID]->on(
                'close',
                function (int $code, string $reason) use ($shardID) {
                    $this->client->emit(
                        'debug',
                        'Shard '.$shardID.' disconnected with code '.$code.' and reason "'.$reason.'"'
                    );

                    $shard = $this->client->shards->get($shardID);
                    if ($shard !== null) {
                        $this->client->emit('disconnect', $shard, $code, $reason);
                    }
                }
            );
        }
    }

    /**
     * Handles the connect encoding for the query string.
     *
     * @param  array  $querystring
     *
     * @return void
     * @throws RuntimeException
     */
    protected function handleConnectEncoding(array &$querystring)
    {
        if ($this->encoding === null) {
            $encoding = $querystring['encoding'] ?? self::WS['encoding'];

            $name = str_replace('-', '', ucwords($encoding, '-'));
            if (strpos($name, '\\') === false) {
                $name = '\\CharlotteDunois\\Yasmin\\WebSocket\\Encoding\\'.$name;
            }

            $name::supported();

            $interfaces = class_implements($name);
            if (! in_array('CharlotteDunois\\Yasmin\\Interfaces\\WSEncodingInterface', $interfaces)) {
                throw new RuntimeException('Specified WS encoding class does not implement necessary interface');
            }

            $this->encoding = new $name();
            $querystring['encoding'] = $this->encoding->getName();
        }
    }

    /**
     * Handles the connect gateway URL in terms to the query string..
     *
     * @param  string  $gateway
     * @param  array  $querystring
     *
     * @return string
     * @throws RuntimeException
     */
    protected function handleConnectGateway(string $gateway, array &$querystring)
    {
        if (! empty($querystring)) {
            if ($this->compression !== '') {
                $compression = $this->compression;
                $querystring['compress'] = $compression::getName();
            }

            $this->gatewayQS = $querystring;
            $gateway = rtrim($gateway, '/').'/?'.http_build_query($querystring);
        }

        return $gateway;
    }
}
