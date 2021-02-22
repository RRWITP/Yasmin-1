<?php

namespace CharlotteDunois\Yasmin\Interfaces;

use BadMethodCallException;
use CharlotteDunois\Collect\Collection;
use CharlotteDunois\Yasmin\Models\CategoryChannel;
use CharlotteDunois\Yasmin\Models\Guild;
use CharlotteDunois\Yasmin\Models\GuildMember;
use CharlotteDunois\Yasmin\Models\Permissions;
use CharlotteDunois\Yasmin\Models\Role;
use InvalidArgumentException;
use React\Promise\ExtendedPromiseInterface;

/**
 * Interface GuildChannelInterface
 *
 * Something all guild channels implement.
 *
 * @method string                getName()                  Gets the channel's name.
 * @method Guild                 getGuild()                 Gets the associated guild.
 * @method int                   getPosition()              Gets the channel's position.
 * @method Collection            getPermissionOverwrites()  Gets the channel's permission overwrites.
 * @method CategoryChannel|null  getParent()                Gets the channel's parent, or null.
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 */
interface GuildChannelInterface extends ChannelInterface
{
    /**
     * Clones a guild channel. Resolves with an instance of GuildChannelInterface.
     *
     * @param  string  $name
     * @param  bool  $withPermissions
     * @param  bool  $withTopic
     * @param  string  $reason
     *
     * @return ExtendedPromiseInterface
     */
    public function clone(
        ?string $name = null,
        bool $withPermissions = true,
        bool $withTopic = true,
        string $reason = ''
    );

    /**
     * Edits the channel. Resolves with $this.
     *
     * Options are as following (at least one is required).
     *
     * ```
     * array(
     *    'name' => string,
     *    'position' => int,
     *    'topic' => string, (text channels only)
     *    'nsfw' => bool, (text channels only)
     *    'bitrate' => int, (voice channels only)
     *    'userLimit' => int, (voice channels only)
     *    'slowmode' => int, (text channels only)
     *    'parent' => \CharlotteDunois\Yasmin\Models\CategoryChannel|string, (string = channel ID)
     *    'permissionOverwrites' => \CharlotteDunois\Collect\Collection|array (an array or Collection of PermissionOverwrite instances or permission overwrite arrays)
     * )
     * ```
     *
     * @param  array  $options
     * @param  string  $reason
     *
     * @return ExtendedPromiseInterface
     * @throws InvalidArgumentException
     */
    public function edit(array $options, string $reason = '');

    /**
     * Deletes the channel.
     *
     * @param  string  $reason
     *
     * @return ExtendedPromiseInterface
     */
    public function delete(string $reason = '');

    /**
     * Returns the permissions for the given member.
     *
     * @param  GuildMember|string  $member
     *
     * @return Permissions
     * @throws InvalidArgumentException
     */
    public function permissionsFor($member);

    /**
     * Returns the permissions overwrites for the given member.
     *
     * ```
     * array(
     *     'everyone' => \CharlotteDunois\Yasmin\Models\PermissionOverwrite|null,
     *     'member' => \CharlotteDunois\Yasmin\Models\PermissionOverwrite|null,
     *     'roles' => \CharlotteDunois\Yasmin\Models\PermissionOverwrite[]
     * )
     * ```
     *
     * @param  GuildMember|string  $member
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public function overwritesFor($member);

    /**
     * Overwrites the permissions for a member or role in this channel. Resolves with an instance of PermissionOverwrite.
     *
     * @param  GuildMember|Role|string  $memberOrRole
     * @param  Permissions|int  $allow
     * @param  Permissions|int  $deny
     * @param  string  $reason
     *
     * @return ExtendedPromiseInterface
     * @throws InvalidArgumentException
     */
    public function overwritePermissions($memberOrRole, $allow, $deny = 0, string $reason = '');

    /**
     * Locks in the permission overwrites from the parent channel. Resolves with $this.
     *
     * @param  string  $reason
     *
     * @return ExtendedPromiseInterface
     * @throws BadMethodCallException
     */
    public function lockPermissions(string $reason = '');

    /**
     * Sets the name of the channel. Resolves with $this.
     *
     * @param  string  $name
     * @param  string  $reason
     *
     * @return ExtendedPromiseInterface
     * @throws InvalidArgumentException
     */
    public function setName(string $name, string $reason = '');

    /**
     * Sets the nsfw flag of the channel. Resolves with $this.
     *
     * @param  bool  $nsfw
     * @param  string  $reason
     *
     * @return ExtendedPromiseInterface
     * @throws InvalidArgumentException
     */
    public function setNSFW(bool $nsfw, string $reason = '');

    /**
     * Sets the parent of the channel. Resolves with $this.
     *
     * @param  CategoryChannel|string  $parent
     * @param  string  $reason
     *
     * @return ExtendedPromiseInterface
     * @throws InvalidArgumentException
     */
    public function setParent($parent, string $reason = '');

    /**
     * Sets the permission overwrites of the channel. Resolves with $this.
     *
     * @param  Collection|array  $permissionOverwrites
     * @param  string  $reason
     *
     * @return ExtendedPromiseInterface
     * @throws InvalidArgumentException
     */
    public function setPermissionOverwrites($permissionOverwrites, string $reason = '');

    /**
     * Sets the position of the channel. Resolves with $this.
     *
     * @param  int  $position
     * @param  string  $reason
     *
     * @return ExtendedPromiseInterface
     * @throws InvalidArgumentException
     */
    public function setPosition(int $position, string $reason = '');
}
