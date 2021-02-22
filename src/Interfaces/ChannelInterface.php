<?php

namespace CharlotteDunois\Yasmin\Interfaces;

/**
 * Interface ChannelInterface
 *
 * Something all channels implement.
 *
 * @method string  getId()                Gets the channel's ID.
 * @method int     getCreatedTimestamp()  Gets the timestamp of when this channel was created.
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 */
interface ChannelInterface
{
    /**
     * Internally patches the instance.
     */
    public function _patch(array $data);
}
