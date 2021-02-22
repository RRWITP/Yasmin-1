<?php

namespace CharlotteDunois\Yasmin\HTTP\Endpoints;

use CharlotteDunois\Yasmin\HTTP\APIManager;

/**
 * Class Voice
 *
 * Handles the API endpoints "Voice".
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 * @internal
 */
class Voice
{
    /**
     * Endpoints Voice.
     *
     * @var array
     */
    const ENDPOINTS = [
        'regions' => 'voice/regions',
    ];

    /**
     * @var APIManager
     */
    protected $api;

    /**
     * Constructor.
     *
     * @param  APIManager  $api
     */
    public function __construct(APIManager $api)
    {
        $this->api = $api;
    }

    public function listVoiceRegions()
    {
        $url = self::ENDPOINTS['regions'];

        return $this->api->makeRequest('GET', $url, []);
    }
}
