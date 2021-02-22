<?php

namespace CharlotteDunois\Yasmin\Models;

use CharlotteDunois\Yasmin\Client;
use Exception;

/**
 * Class ClientBase
 *
 * Something all Models, with the need for a client, extend.
 *
 * @property Client $client  The client which initiated the instance.
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 */
abstract class ClientBase extends Base
{
    /**
     * @var Client
     * @internal
     */
    protected $client;

    /**
     * The client which will be used to unserialize.
     *
     * @var Client|null
     */
    public static $serializeClient;

    /**
     * @param  Client  $client
     *
     * @internal
     */
    public function __construct(Client $client)
    {
        parent::__construct();

        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     * @return mixed
     * @internal
     */
    public function __get($name)
    {
        switch ($name) {
            case 'client':
                return $this->client;
                break;
        }

        return parent::__get($name);
    }

    /**
     * @return mixed
     * @internal
     */
    public function __debugInfo()
    {
        $vars = get_object_vars($this);
        unset($vars['client']);

        return $vars;
    }

    /**
     * @return mixed
     * @internal
     */
    public function jsonSerialize()
    {
        $vars = parent::jsonSerialize();
        unset($vars['client']);

        return $vars;
    }

    /**
     * @return string
     * @internal
     */
    public function serialize()
    {
        $vars = get_object_vars($this);
        unset($vars['client']);

        return serialize($vars);
    }

    /**
     * @return void
     * @throws Exception
     * @internal
     */
    public function unserialize($data)
    {
        if (self::$serializeClient === null) {
            throw new Exception('Unable to unserialize a class without ClientBase::$serializeClient being set');
        }

        parent::unserialize($data);

        $this->client = self::$serializeClient;
    }
}
