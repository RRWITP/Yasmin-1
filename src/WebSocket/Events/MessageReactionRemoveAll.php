<?php

namespace CharlotteDunois\Yasmin\WebSocket\Events;

use CharlotteDunois\Yasmin\Client;
use CharlotteDunois\Yasmin\Interfaces\TextChannelInterface;
use CharlotteDunois\Yasmin\Interfaces\WSEventInterface;
use CharlotteDunois\Yasmin\Models\Message;
use CharlotteDunois\Yasmin\WebSocket\WSConnection;
use CharlotteDunois\Yasmin\WebSocket\WSManager;
use function React\Promise\resolve;

/**
 * Class MessageReactionRemoveAll
 *
 * WS Event.
 *
 * @see https://discordapp.com/developers/docs/topics/gateway#message-reaction-remove-all
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 * @internal
 */
class MessageReactionRemoveAll implements WSEventInterface
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
        $channel = $this->client->channels->get($data['channel_id']);
        if ($channel instanceof TextChannelInterface) {
            $message = $channel->getMessages()->get($data['message_id']);
            if ($message) {
                $message = resolve($message);
            } else {
                $message = $channel->fetchMessage($data['message_id']);
            }

            $message->done(
                function (Message $message) {
                    $message->reactions->clear();
                    $this->client->queuedEmit('messageReactionRemoveAll', $message);
                },
                function () {
                    // Don't handle it
                }
            );
        }
    }
}
