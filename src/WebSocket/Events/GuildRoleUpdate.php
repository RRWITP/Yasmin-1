<?php

namespace CharlotteDunois\Yasmin\WebSocket\Events;

use CharlotteDunois\Yasmin\Client;
use CharlotteDunois\Yasmin\Interfaces\WSEventInterface;
use CharlotteDunois\Yasmin\WebSocket\WSConnection;
use CharlotteDunois\Yasmin\WebSocket\WSManager;

/**
 * Class GuildRoleUpdate
 *
 * WS Event.
 *
 * @see https://discordapp.com/developers/docs/topics/gateway#guild-role-update
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 * @internal
 * @internal
 */
class GuildRoleUpdate implements WSEventInterface
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
        $this->clones = ! ($clones === true || in_array('roleUpdate', (array) $clones));
    }

    public function handle(WSConnection $ws, $data): void
    {
        $guild = $this->client->guilds->get($data['guild_id']);
        if ($guild) {
            $role = $guild->roles->get($data['role']['id']);
            if ($role) {
                $oldRole = null;
                if ($this->clones) {
                    $oldRole = clone $role;
                }

                $role->_patch($data['role']);
                $this->client->queuedEmit('roleUpdate', $role, $oldRole);
            } else {
                $role = $guild->roles->factory($data['role']);
                $this->client->queuedEmit('roleCreate', $role);
            }
        }
    }
}
