<?php

namespace CharlotteDunois\Yasmin\WebSocket\Events;

use CharlotteDunois\Yasmin\Client;
use CharlotteDunois\Yasmin\Interfaces\WSEventInterface;
use CharlotteDunois\Yasmin\Models\User;
use CharlotteDunois\Yasmin\WebSocket\WSConnection;
use CharlotteDunois\Yasmin\WebSocket\WSManager;
use function React\Promise\resolve;

/**
 * Class GuildBanRemove
 *
 * WS Event.
 *
 * @see https://discordapp.com/developers/docs/topics/gateway#guild-ban-remove
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 * @internal
 */
class GuildBanRemove implements WSEventInterface
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
            $user = $this->client->users->patch($data['user']);
            if ($user) {
                $user = resolve($user);
            } else {
                $user = $this->client->fetchUser($data['user']['id']);
            }

            $user->done(
                function (User $user) use ($guild) {
                    $this->client->queuedEmit('guildBanRemove', $guild, $user);
                },
                [$this->client, 'handlePromiseRejection']
            );
        }
    }
}
