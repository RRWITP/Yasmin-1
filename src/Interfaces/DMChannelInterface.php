<?php

namespace CharlotteDunois\Yasmin\Interfaces;

use CharlotteDunois\Yasmin\Models\User;
use InvalidArgumentException;

/**
 * Interface DMChannelInterface
 *
 * Something all direct message channels implement.
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 */
interface DMChannelInterface extends ChannelInterface, TextChannelInterface
{
    /**
     * Determines whether a given user is a recipient of this channel.
     *
     * @param  User|string  $user  The User instance or user ID.
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function isRecipient($user);
}
