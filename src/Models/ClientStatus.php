<?php

namespace CharlotteDunois\Yasmin\Models;

use RuntimeException;

/**
 * Class ClientStatus
 *
 * Represents an user's client status.
 *
 * @property string|null $desktop  The status of the user on the desktop client.
 * @property string|null $mobile   The status of the user on the mobile client.
 * @property string|null $web      The status of the user on the web client.
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 */
class ClientStatus extends Base
{
    /**
     * Client status: online.
     *
     * @var string
     * @source
     */
    const STATUS_ONLINE = 'online';

    /**
     * Client status: do not disturb.
     *
     * @var string
     * @source
     */
    const STATUS_DND = 'dnd';

    /**
     * Client status: idle.
     *
     * @var string
     * @source
     */
    const STATUS_IDLE = 'idle';

    /**
     * Client status: offline.
     *
     * @var string
     * @source
     */
    const STATUS_OFFLINE = 'offline';

    /**
     * The status of the user on the desktop client.
     *
     * @var string|null
     */
    protected $desktop;

    /**
     * The status of the user on the mobile client.
     *
     * @var string|null
     */
    protected $mobile;

    /**
     * The status of the user on the web client.
     *
     * @var string|null
     */
    protected $web;

    /**
     * Constructs a new instance.
     *
     * @param  array  $clientStatus  An array containing the client status data.
     *
     * @internal
     */
    public function __construct(array $clientStatus)
    {
        parent::__construct();

        $this->desktop = $clientStatus['desktop'] ?? null;
        $this->mobile = $clientStatus['mobile'] ?? null;
        $this->web = $clientStatus['web'] ?? null;
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
     * @return mixed
     * @internal
     */
    public function jsonSerialize()
    {
        return [
            'desktop' => $this->desktop,
            'mobile'  => $this->mobile,
            'web'     => $this->web,
        ];
    }
}
