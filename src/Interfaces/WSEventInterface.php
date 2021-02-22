<?php

namespace CharlotteDunois\Yasmin\Interfaces;

use CharlotteDunois\Yasmin\Client;
use CharlotteDunois\Yasmin\WebSocket\WSConnection;
use CharlotteDunois\Yasmin\WebSocket\WSManager;

/**
 * Interface WSEventInterface
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 * @internal
 */
interface WSEventInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        Client $client,
        WSManager $wsmanager
    );

    /**
     * Handles events.
     *
     * @return void
     */
    public function handle(WSConnection $ws, $data): void;
}
