<?php

namespace CharlotteDunois\Yasmin\WebSocket\Events;

use CharlotteDunois\Collect\Collection;
use CharlotteDunois\Yasmin\Client;
use CharlotteDunois\Yasmin\Interfaces\WSEventInterface;
use CharlotteDunois\Yasmin\WebSocket\WSConnection;
use CharlotteDunois\Yasmin\WebSocket\WSManager;

/**
 * Class GuildMembersChunk
 *
 * WS Event.
 *
 * @see https://discordapp.com/developers/docs/topics/gateway#guild-members-chunk
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 * @internal
 */
class GuildMembersChunk implements WSEventInterface
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
            $members = new Collection();
            foreach ($data['members'] as $mdata) {
                $member = $guild->_addMember($mdata, true);
                $members->set($member->id, $member);
            }

            $this->client->queuedEmit('guildMembersChunk', $guild, $members);
        }
    }
}
