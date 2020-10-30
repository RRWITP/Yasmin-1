<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved.
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 */

namespace CharlotteDunois\Yasmin\WebSocket\Encoding;

use CharlotteDunois\Kimberly\Atom;
use CharlotteDunois\Kimberly\BaseObject;
use CharlotteDunois\Kimberly\Exception;
use CharlotteDunois\Kimberly\Kimberly;
use CharlotteDunois\Yasmin\Interfaces\WSEncodingInterface;
use CharlotteDunois\Yasmin\WebSocket\DiscordGatewayException;
use Ratchet\RFC6455\Messaging\Frame;
use Ratchet\RFC6455\Messaging\Message;
use RuntimeException;
use function class_exists;
use function is_array;
use function is_int;
use function is_object;
use function is_string;
use function mb_substr;
use const PHP_INT_SIZE;

/**
 * Handles WS encoding.
 *
 * @internal
 */
class Etf implements WSEncodingInterface
{
    /**
     * @var Kimberly
     */
    protected $etf;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->etf = new Kimberly();
    }

    /**
     * Returns encoding name (for gateway query string).
     *
     * @return string
     */
    public function getName(): string
    {
        return 'etf';
    }

    /**
     * Checks if the system supports it.
     *
     * @return void
     * @throws RuntimeException
     */
    public static function supported(): void
    {
        if (! class_exists('\\CharlotteDunois\\Kimberly\\Kimberly')) {
            throw new RuntimeException('Unable to use ETF as WS encoding due to missing dependencies');
        }

        if (PHP_INT_SIZE < 8) {
            throw new RuntimeException('ETF can not be used on with 32 bit PHP');
        }
    }

    /**
     * Decodes data.
     *
     * @param  string  $data
     *
     * @return mixed
     * @throws DiscordGatewayException
     */
    public function decode(string $data)
    {
        try {
            $msg = $this->etf->decode($data);
            if ($msg === '' || $msg === null) {
                throw new DiscordGatewayException(
                    'The ETF decoder was unable to decode the data'
                );
            }
        } catch (Exception $e) {
            throw new DiscordGatewayException(
                'The ETF decoder was unable to decode the data', 0, $e
            );
        }

        $obj = $this->convertIDs($msg);

        return $obj;
    }

    /**
     * Encodes data.
     *
     * @param  mixed  $data
     *
     * @return string
     * @throws DiscordGatewayException
     */
    public function encode($data): string
    {
        try {
            return $this->etf->encode($data);
        } catch (Exception $e) {
            throw new DiscordGatewayException(
                'The ETF encoder was unable to encode the data', 0, $e
            );
        }
    }

    /**
     * Prepares the data to be sent.
     *
     * @param  string  $data
     *
     * @return Message
     */
    public function prepareMessage(string $data): Message
    {
        $frame = new Frame($data, true, Frame::OP_BINARY);

        $msg = new Message();
        $msg->addFrame($frame);

        return $msg;
    }

    /**
     * Converts all IDs from integer to strings.
     *
     * @param  array|object  $data
     *
     * @return array|object
     */
    protected function convertIDs($data)
    {
        $arr = [];

        foreach ($data as $key => $val) {
            if (is_string($key) && $key[0] === ':') {
                $key = mb_substr($key, 1);
            }

            if ($val instanceof Atom) {
                $arr[$key] = (string) $val->atom;
            } elseif ($val instanceof BaseObject) {
                $arr[$key] = $val->toArray();
            } elseif (is_array($val) || is_object($val)) {
                $arr[$key] = $this->convertIDs($val);
            } else {
                if (is_int($val) && ($key === 'id' || mb_substr($key, -3) === '_id')) {
                    $val = (string) $val;
                }

                $arr[$key] = $val;
            }
        }

        return is_object($data) ? ((object) $arr) : $arr;
    }
}
