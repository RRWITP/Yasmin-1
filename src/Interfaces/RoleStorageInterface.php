<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved.
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 */

namespace CharlotteDunois\Yasmin\Interfaces;

use CharlotteDunois\Yasmin\Models\Role;
use InvalidArgumentException;

/**
 * Something all role storages implement. The storage also is used as factory.
 */
interface RoleStorageInterface extends StorageInterface
{
    /**
     * Returns the current element. From Iterator interface.
     *
     * @return Role
     */
    public function current();

    /**
     * Fetch the key from the current element. From Iterator interface.
     *
     * @return string
     */
    public function key();

    /**
     * Advances the internal pointer. From Iterator interface.
     *
     * @return Role|false
     */
    public function next();

    /**
     * Resets the internal pointer. From Iterator interface.
     *
     * @return Role|false
     */
    public function rewind();

    /**
     * Checks if current position is valid. From Iterator interface.
     *
     * @return bool
     */
    public function valid();

    /**
     * Returns all items.
     *
     * @return Role[]
     */
    public function all();

    /**
     * Resolves given data to a Role.
     *
     * @param  Role|string|int  $role  string/int = role ID
     *
     * @return Role
     * @throws InvalidArgumentException
     */
    public function resolve($role);

    /**
     * Returns the item at a given key. If the key does not exist, null is returned.
     *
     * @param  string  $key
     *
     * @return Role|null
     * @throws InvalidArgumentException
     */
    public function get($key);

    /**
     * Sets a key-value pair.
     *
     * @param  string  $key
     * @param  Role  $value
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function set($key, $value);

    /**
     * Factory to create (or retrieve existing) roles.
     *
     * @param  array  $data
     *
     * @return Role
     * @internal
     */
    public function factory(array $data);
}
