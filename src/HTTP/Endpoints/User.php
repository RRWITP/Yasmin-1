<?php

namespace CharlotteDunois\Yasmin\HTTP\Endpoints;

use CharlotteDunois\Yasmin\HTTP\APIEndpoints;
use CharlotteDunois\Yasmin\HTTP\APIManager;

/**
 * Class User
 *
 * Handles the API endpoints "User".
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 * @internal
 */
class User
{
    /**
     * Endpoints Users.
     *
     * @var array
     */
    const ENDPOINTS = [
        'get'     => 'users/%s',
        'current' => [
            'get'           => 'users/@me',
            'modify'        => 'users/@me',
            'guilds'        => 'users/@me/guilds',
            'leaveGuild'    => 'users/@me/guilds/%s',
            'dms'           => 'users/@me/channels',
            'createDM'      => 'users/@me/channels',
            'createGroupDM' => 'users/@me/channels',
            'connections'   => 'users/@me/connections',
        ],
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

    public function getCurrentUser()
    {
        $url = APIEndpoints::format(self::ENDPOINTS['current']['get']);

        return $this->api->makeRequest('GET', $url, []);
    }

    public function getUser(string $userid)
    {
        $url = APIEndpoints::format(self::ENDPOINTS['get'], $userid);

        return $this->api->makeRequest('GET', $url, []);
    }

    public function modifyCurrentUser(array $options)
    {
        $url = APIEndpoints::format(self::ENDPOINTS['current']['modify']);

        return $this->api->makeRequest('PATCH', $url, ['data' => $options]);
    }

    public function getCurrentUserGuilds()
    {
        $url = APIEndpoints::format(self::ENDPOINTS['current']['guilds']);

        return $this->api->makeRequest('GET', $url, []);
    }

    public function leaveUserGuild(string $guildid)
    {
        $url = APIEndpoints::format(self::ENDPOINTS['current']['leaveGuild'], $guildid);

        return $this->api->makeRequest('DELETE', $url, []);
    }

    public function getUserDMs()
    {
        $url = APIEndpoints::format(self::ENDPOINTS['current']['dms']);

        return $this->api->makeRequest('GET', $url, []);
    }

    public function createUserDM(string $recipientid)
    {
        $url = APIEndpoints::format(self::ENDPOINTS['current']['createDM']);

        return $this->api->makeRequest('POST', $url, ['data' => ['recipient_id' => $recipientid]]);
    }

    public function createGroupDM(array $accessTokens, array $nicks)
    {
        $url = APIEndpoints::format(self::ENDPOINTS['current']['createGroupDM']);

        return $this->api->makeRequest('POST', $url, ['data' => ['access_tokens' => $accessTokens, 'nicks' => $nicks]]);
    }

    public function getUserConnections(string $accessToken)
    {
        $url = APIEndpoints::format(self::ENDPOINTS['current']['connections']);

        return $this->api->makeRequest('GET', $url, ['auth' => 'Bearer '.$accessToken]);
    }
}
