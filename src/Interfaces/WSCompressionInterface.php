<?php

namespace CharlotteDunois\Yasmin\Interfaces;

use CharlotteDunois\Yasmin\WebSocket\DiscordGatewayException;
use Exception;

/**
 * Interface WSCompressionInterface
 *
 * Interface for WS compressions. This is used internally.
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 */
interface WSCompressionInterface
{
    /**
     * Checks if the system supports it.
     *
     * @return void
     * @throws Exception
     */
    public static function supported(): void;

    /**
     * Returns compression name (for gateway query string).
     *
     * @return string
     */
    public static function getName(): string;

    /**
     * Returns a boolean for the OP code 2 IDENTIFY packet 'compress' parameter. The parameter is for payload compression.
     *
     * @return bool
     */
    public static function isPayloadCompression(): bool;

    /**
     * Initializes the context.
     *
     * @return void
     * @throws Exception
     */
    public function init(): void;

    /**
     * Destroys the context.
     *
     * @return void
     */
    public function destroy(): void;

    /**
     * Decompresses data.
     *
     * @param  string  $data
     *
     * @return string
     * @throws DiscordGatewayException
     */
    public function decompress(string $data): string;
}
