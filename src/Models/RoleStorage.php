<?php

namespace CharlotteDunois\Yasmin\Models;

use CharlotteDunois\Yasmin\Client;
use CharlotteDunois\Yasmin\Interfaces\RoleStorageInterface;
use InvalidArgumentException;

/**
 * Class RoleStorage
 *
 * Role Storage to store a guild's roles, utilizes Collection.
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 */
class RoleStorage extends Storage implements RoleStorageInterface
{
    /**
     * The guild this storage belongs to.
     *
     * @var Guild
     */
    protected $guild;

    /**
     * @param  Client  $client
     * @param  Guild  $guild
     * @param  array|null  $data
     *
     * @internal
     */
    public function __construct(Client $client, Guild $guild, array $data = null)
    {
        parent::__construct($client, $data);
        $this->guild = $guild;

        $this->baseStorageArgs[] = $this->guild;
    }

    /**
     * Resolves given data to a Role.
     *
     * @param  Role|string|int  $role  string/int = role ID
     *
     * @return Role
     * @throws InvalidArgumentException
     */
    public function resolve($role)
    {
        if ($role instanceof Role) {
            return $role;
        }

        if (is_int($role)) {
            $role = (string) $role;
        }

        if (is_string($role) && parent::has($role)) {
            return parent::get($role);
        }

        throw new InvalidArgumentException('Unable to resolve unknown role');
    }

    /**
     * {@inheritdoc}
     * @param  string  $key
     *
     * @return bool
     */
    public function has($key)
    {
        return parent::has($key);
    }

    /**
     * {@inheritdoc}
     * @param  string  $key
     *
     * @return Role|null
     */
    public function get($key)
    {
        return parent::get($key);
    }

    /**
     * {@inheritdoc}
     * @param  string  $key
     * @param  Role  $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        parent::set($key, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     * @param  string  $key
     *
     * @return $this
     */
    public function delete($key)
    {
        parent::delete($key);

        return $this;
    }

    /**
     * Factory to create (or retrieve existing) roles.
     *
     * @param  array  $data
     *
     * @return Role
     * @internal
     */
    public function factory(array $data)
    {
        if (parent::has($data['id'])) {
            $role = parent::get($data['id']);
            $role->_patch($data);

            return $role;
        }

        $role = new Role($this->client, $this->guild, $data);
        $this->set($role->id, $role);

        return $role;
    }
}
