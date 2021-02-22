<?php

namespace CharlotteDunois\Yasmin\Models;

use CharlotteDunois\Yasmin\Client;
use CharlotteDunois\Yasmin\WebSocket\WSConnection;
use RuntimeException;
use Serializable;

/**
 * Class Shard
 *
 * Represents a shard.
 *
 * @property int $id  The shard ID.
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 */
class Shard extends ClientBase implements Serializable
{
    /**
     * The shard ID.
     *
     * @var int
     */
    protected $id;

    /**
     * The websocket connection of this shard.
     *
     * @var WSConnection
     */
    protected $ws;

    /**
     * @param  Client  $client
     * @param  int  $shardID
     * @param  WSConnection  $connection
     *
     * @internal
     */
    public function __construct(Client $client, int $shardID, WSConnection $connection)
    {
        parent::__construct($client);

        $this->id = $shardID;
        $this->ws = $connection;
    }

    /**
     * {@inheritdoc}
     * @return mixed
     * @throws RuntimeException
     * @internal
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        return parent::__get($name);
    }

    /**
     * @return string
     * @internal
     */
    public function serialize()
    {
        $vars = get_object_vars($this);
        unset($vars['client'], $vars['ws']);

        return serialize($vars);
    }

    /**
     * @return string
     * @internal
     */
    public function __toString()
    {
        return (string) $this->id;
    }
}
