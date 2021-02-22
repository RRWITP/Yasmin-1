<?php

namespace CharlotteDunois\Yasmin\Interfaces;

use InvalidArgumentException;
use React\Promise\ExtendedPromiseInterface;

/**
 * Interface GuildVoiceChannelInterface
 *
 * Something all guild voice channels implement.
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 */
interface GuildVoiceChannelInterface extends GuildChannelInterface, VoiceChannelInterface
{
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
