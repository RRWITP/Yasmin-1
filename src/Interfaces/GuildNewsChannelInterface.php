<?php

namespace CharlotteDunois\Yasmin\Interfaces;

use InvalidArgumentException;
use React\Promise\ExtendedPromiseInterface;

/**
 * Interface GuildNewChannelInterface
 *
 * Something all guild news channels implement.
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 */
interface GuildNewsChannelInterface extends GuildChannelInterface, TextChannelInterface
{
    /**
     * Creates an invite. Resolves with an instance of Invite.
     *
     * Options are as following (all are optional).
     *
     * ```
     * array(
     *    'maxAge' => int,
     *    'maxUses' => int, (0 = unlimited)
     *    'temporary' => bool,
     *    'unique' => bool
     * )
     * ```
     *
     * @param  array  $options
     *
     * @return ExtendedPromiseInterface
     */
    public function createInvite(array $options = []);

    /**
     * Fetches all invites of this channel. Resolves with a Collection of Invite instances, mapped by their code.
     *
     * @return ExtendedPromiseInterface
     * @see \CharlotteDunois\Yasmin\Models\Invite
     */
    public function fetchInvites();

    /**
     * Sets the topic of the channel. Resolves with $this.
     *
     * @param  string  $topic
     * @param  string  $reason
     *
     * @return ExtendedPromiseInterface
     * @throws InvalidArgumentException
     */
    public function setTopic(string $topic, string $reason = '');
}
