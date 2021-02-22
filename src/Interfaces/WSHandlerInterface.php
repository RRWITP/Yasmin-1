<?php

namespace CharlotteDunois\Yasmin\Interfaces;

use CharlotteDunois\Yasmin\WebSocket\WSConnection;
use CharlotteDunois\Yasmin\WebSocket\WSHandler;

/**
 * Interface WSHandlerInterface
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 * @internal
 */
interface WSHandlerInterface
{
    /**
     * Constructor.
     */
    public function __construct(WSHandler $wshandler);

    /**
     * Handles packets.
     *
     * @return void
     */
    public function handle(WSConnection $ws, $packet): void;
}
