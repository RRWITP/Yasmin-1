<?php

namespace CharlotteDunois\Yasmin\WebSocket\Events;

use CharlotteDunois\Yasmin\Client;
use CharlotteDunois\Yasmin\Interfaces\WSEventInterface;
use CharlotteDunois\Yasmin\WebSocket\WSConnection;
use CharlotteDunois\Yasmin\WebSocket\WSManager;

/**
 * Class GuildMemberUpdate
 *
 * WS Event.
 *
 * @see https://discordapp.com/developers/docs/topics/gateway#guild-member-update
 * @internal
 */
class GuildMemberUpdate implements WSEventInterface
{
    /**
     * The client.
     *
     * @var Client
     */
    protected $client;

    /**
     * Whether we do clones.
     *
     * @var bool
     */
    protected $clones = false;

    public function __construct(
        Client $client,
        WSManager $wsmanager
    ) {
        $this->client = $client;

        $clones = $this->client->getOption('disableClones', []);
        $this->clones = ! ($clones === true || in_array('guildMemberUpdate', (array) $clones));
    }

    public function handle(WSConnection $ws, $data): void
    {
        $guild = $this->client->guilds->get($data['guild_id']);
        if ($guild) {
            $guildmember = $guild->members->get($data['user']['id']);
            if ($guildmember) {
                $oldMember = null;
                if ($this->clones) {
                    $oldMember = clone $guildmember;
                }

                $guildmember->_patch($data);
                $this->client->queuedEmit('guildMemberUpdate', $guildmember, $oldMember);
            } else {
                $guild->fetchMember($data['user']['id'])->done();
            }
        }
    }
}
