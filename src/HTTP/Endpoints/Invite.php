<?php

namespace CharlotteDunois\Yasmin\HTTP\Endpoints;

use CharlotteDunois\Yasmin\HTTP\APIEndpoints;
use CharlotteDunois\Yasmin\HTTP\APIManager;

/**
 * Class Invite
 *
 * Handles the API endpoints "Invite".
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 * @internal
 */
class Invite
{
    /**
     * Endpoints Invites.
     *
     * @var array
     */
    const ENDPOINTS = [
        'get'    => 'invites/%s',
        'delete' => 'invites/%s',
        'accept' => 'invites/%s',
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

    public function getInvite(string $code, bool $withCounts = false)
    {
        $url = APIEndpoints::format(self::ENDPOINTS['get'], $code);

        $opts = [];
        if ($withCounts) {
            $opts['querystring'] = ['with_counts' => 'true'];
        }

        return $this->api->makeRequest('GET', $url, $opts);
    }

    public function deleteInvite(string $code, string $reason = '')
    {
        $url = APIEndpoints::format(self::ENDPOINTS['delete'], $code);

        return $this->api->makeRequest('DELETE', $url, ['auditLogReason' => $reason]);
    }

    public function acceptInvite(string $code)
    {
        $url = APIEndpoints::format(self::ENDPOINTS['accept'], $code);

        return $this->api->makeRequest('POST', $url, []);
    }
}
