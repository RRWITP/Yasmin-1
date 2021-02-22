<?php

namespace CharlotteDunois\Yasmin\Interfaces;

use CharlotteDunois\Yasmin\WebSocket\DiscordGatewayException;
use Ratchet\RFC6455\Messaging\Message;
use RuntimeException;

/**
 * Interface WSEncodingInterface
 *
 * Interface for WS encodings. This is used internally.
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 */
interface WSEncodingInterface
{
    /**
     * Returns encoding name (for gateway query string).
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Checks if the system supports it.
     *
     * @return void
     * @throws RuntimeException
     */
    public static function supported(): void;

    /**
     * Decodes data.
     *
     * @param  string  $data
     *
     * @return mixed
     * @throws DiscordGatewayException
     */
    public function decode(string $data);

    /**
     * Encodes data.
     *
     * @param  mixed  $data
     *
     * @return string
     * @throws DiscordGatewayException
     */
    public function encode($data): string;

    /**
     * Prepares the data to be sent.
     *
     * @param  string  $data
     *
     * @return Message
     */
    public function prepareMessage(string $data): Message;
}
