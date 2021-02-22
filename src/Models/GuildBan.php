<?php

namespace CharlotteDunois\Yasmin\Models;

use CharlotteDunois\Yasmin\Client;
use React\Promise\ExtendedPromiseInterface;
use RuntimeException;

/**
 * Class GuildBan
 *
 * Represents a guild ban.
 *
 * @property Guild       $guild   The guild this ban is from.
 * @property User        $user    The banned user.
 * @property string|null $reason  The ban reason, or null.
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 */
class GuildBan extends ClientBase
{
    /**
     * The guild this ban is from.
     *
     * @var Guild
     */
    protected $guild;

    /**
     * The banned user.
     *
     * @var User
     */
    protected $user;

    /**
     * The ban reason, or null.
     *
     * @var string|null
     */
    protected $reason;

    /**
     * @param  Client  $client
     * @param  Guild  $guild
     * @param  User  $user
     * @param  string|null  $reason
     *
     * @internal
     */
    public function __construct(
        Client $client,
        Guild $guild,
        User $user,
        ?string $reason
    ) {
        parent::__construct($client);

        $this->guild = $guild;
        $this->user = $user;
        $this->reason = $reason;
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
     * Unbans the user.
     *
     * @param  string  $reason
     *
     * @return ExtendedPromiseInterface
     */
    public function unban(string $reason = '')
    {
        return $this->guild->unban($this->user, $reason);
    }
}
