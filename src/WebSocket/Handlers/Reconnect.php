<?php

namespace CharlotteDunois\Yasmin\WebSocket\Handlers;

use CharlotteDunois\Yasmin\Interfaces\WSHandlerInterface;
use CharlotteDunois\Yasmin\WebSocket\WSConnection;
use CharlotteDunois\Yasmin\WebSocket\WSHandler;

/**
 * Class Reconnect
 *
 * WS Event handler.
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 * @internal
 */
class Reconnect implements WSHandlerInterface
{
    protected $wshandler;

    public function __construct(WSHandler $wshandler)
    {
        $this->wshandler = $wshandler;
    }

    public function handle(WSConnection $ws, $packet): void
    {
        $ws->reconnect($packet['d'] ?? true);
    }
}
