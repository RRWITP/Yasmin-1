<?php

namespace CharlotteDunois\Yasmin\WebSocket\Events;

use CharlotteDunois\Yasmin\Client;
use CharlotteDunois\Yasmin\Interfaces\ChannelInterface;
use CharlotteDunois\Yasmin\Interfaces\GuildChannelInterface;
use CharlotteDunois\Yasmin\Interfaces\WSEventInterface;
use CharlotteDunois\Yasmin\WebSocket\WSConnection;
use CharlotteDunois\Yasmin\WebSocket\WSManager;

/**
 * Class ChannelDelete
 *
 * WS Event.
 *
 * @see https://discordapp.com/developers/docs/topics/gateway#channel-delete
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 * @internal
 */
class ChannelDelete implements WSEventInterface
{
    /**
     * The client.
     *
     * @var Client
     */
    protected $client;

    public function __construct(
        Client $client,
        WSManager $wsmanager
    ) {
        $this->client = $client;
    }

    public function handle(WSConnection $ws, $data): void
    {
        $channel = $this->client->channels->get($data['id']);
        if ($channel instanceof ChannelInterface) {
            if ($channel instanceof GuildChannelInterface) {
                $channel->getGuild()->channels->delete($channel->getId());
            }

            $this->client->channels->delete($channel->getId());
            $this->client->queuedEmit('channelDelete', $channel);
        }
    }
}
