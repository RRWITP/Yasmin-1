<?php

namespace CharlotteDunois\Yasmin\Models;

use CharlotteDunois\Collect\Collection;
use CharlotteDunois\Yasmin\Client;
use InvalidArgumentException;
use React\Promise\ExtendedPromiseInterface;
use React\Promise\Promise;
use RuntimeException;

/**
 * Class MessageReaction
 *
 * Represents a message reaction.
 *
 * @property Emoji      $emoji     The emoji this message reaction is for.
 * @property int        $count     Times this emoji has been reacted.
 * @property bool       $me        Whether the current user has reacted using this emoji.
 * @property Message    $message   The message this reaction belongs to.
 * @property Collection $users     The users that have given this reaction, mapped by their ID.
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 */
class MessageReaction extends ClientBase
{
    /**
     * The message this reaction belongs to.
     *
     * @var Message
     */
    protected $message;

    /**
     * The emoji this message reaction is for.
     *
     * @var Emoji
     */
    protected $emoji;

    /**
     * Times this emoji has been reacted.
     *
     * @var int
     */
    protected $count;

    /**
     * Whether the current user has reacted using this emoji.
     *
     * @var bool
     */
    protected $me;

    /**
     * The users that have given this reaction, mapped by their ID.
     *
     * @var Collection
     */
    protected $users;

    /**
     * @internal
     */
    public function __construct(Client $client, Message $message, Emoji $emoji, array $reaction)
    {
        parent::__construct($client);
        $this->message = $message;
        $this->emoji = $emoji;

        $this->count = (int) $reaction['count'];
        $this->me = (bool) $reaction['me'];
        $this->users = new Collection();
    }

    /**
     * {@inheritdoc}
     * @return mixed
     * @throws RuntimeException
     * @internal
     */
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        return parent::__get($name);
    }

    /**
     * Fetches all the users that gave this reaction. Resolves with a Collection of User instances, mapped by their IDs.
     *
     * @param  int  $limit  The maximum amount of users to fetch, defaults to 100.
     * @param  string  $before  Limit fetching users to those with an ID smaller than the given ID.
     * @param  string  $after  Limit fetching users to those with an ID greater than the given ID.
     *
     * @return ExtendedPromiseInterface
     * @see \CharlotteDunois\Yasmin\Models\User
     */
    public function fetchUsers(int $limit = 100, string $before = '', string $after = '')
    {
        return new Promise(function (callable $resolve, callable $reject) use ($limit, $before, $after) {
            $query = ['limit' => $limit];

            if (! empty($before)) {
                $query['before'] = $before;
            }

            if (! empty($after)) {
                $query['after'] = $after;
            }

            $this->client->apimanager()->endpoints->channel
                ->getMessageReactions($this->message->channel->getId(), $this->message->id, $this->emoji->identifier, $query)
                ->done(function ($data) use ($resolve) {
                    foreach ($data as $react) {
                        $user = $this->client->users->patch($react);
                        $this->users->set($user->id, $user);
                    }

                    $resolve($this->users);
                }, $reject);
            }
        );
    }

    /**
     * Removes an user from the reaction. Resolves with $this.
     *
     * @param  User|string  $user  Defaults to the client user.
     *
     * @return ExtendedPromiseInterface
     * @throws InvalidArgumentException
     */
    public function remove($user = null)
    {
        if ($user !== null) {
            $user = $this->client->users->resolve($user);
        }

        return new Promise(
            function (callable $resolve, callable $reject) use ($user) {
                $this->client->apimanager()->endpoints->channel
                    ->deleteMessageUserReaction(
                        $this->message->channel->getId(),
                        $this->message->id,
                        $this->emoji->identifier,
                        ($user !== null ? $user->id : '@me')
                    )
                    ->done(
                        function () use ($resolve) {
                            $resolve($this);
                        },
                        $reject
                    );
            }
        );
    }

    /**
     * Increments the count.
     *
     * @return void
     * @internal
     */
    public function _incrementCount()
    {
        $this->count++;
    }

    /**
     * Decrements the count.
     *
     * @return void
     * @internal
     */
    public function _decrementCount()
    {
        $this->count--;
    }
}
