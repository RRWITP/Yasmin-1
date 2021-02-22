<?php

namespace CharlotteDunois\Yasmin\WebSocket\Events;

use CharlotteDunois\Yasmin\Client;
use CharlotteDunois\Yasmin\Interfaces\WSEventInterface;
use CharlotteDunois\Yasmin\Models\Emoji;
use CharlotteDunois\Yasmin\WebSocket\WSConnection;
use CharlotteDunois\Yasmin\WebSocket\WSManager;

/**
 * Class GuildEmojisUpdate
 *
 * WS Event.
 *
 * @see https://discordapp.com/developers/docs/topics/gateway#guild-emojis-update
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 * @internal
 */
class GuildEmojisUpdate implements WSEventInterface
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
        $guild = $this->client->guilds->get($data['guild_id']);
        if ($guild) {
            $ids = [];
            foreach ($data['emojis'] as $emoji) {
                $ids[] = $emoji['id'];

                if ($guild->emojis->has($emoji['id'])) {
                    $guild->emojis->get($emoji['id'])->_patch($emoji);
                } else {
                    $em = new Emoji($this->client, $guild, $emoji);
                    $guild->emojis->set($em->id, $em);
                }
            }

            foreach ($guild->emojis as $emoji) {
                if (! in_array($emoji->id, $ids)) {
                    $this->client->emojis->delete($emoji->id);
                    $guild->emojis->delete($emoji->id);
                }
            }

            $this->client->queuedEmit('guildEmojisUpdate', $guild);
        }
    }
}
