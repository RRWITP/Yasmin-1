<?php

namespace CharlotteDunois\Yasmin\Utils;

/**
 * Class ImageHelpers
 *
 * Image Helper utilities.
 *
 * @author       Charlotte Dunois (https://charuru.moe)
 * @copyright    2017-2019 Charlotte Dunois
 * @license      https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 * @package      Yasmin
 */
class ImageHelpers
{
    /**
     * Returns the default extension for an image.
     *
     * @param  string  $image  The image hash.
     *
     * @return string  Returns "gif" if the hash begins with "a_", otherwise "png".
     */
    public static function getImageExtension(string $image): string
    {
        return strpos($image, 'a_') === 0 ? 'gif' : 'png';
    }

    /**
     * Returns whether the input number is a power of 2.
     *
     * @param  int|null  $size
     *
     * @return bool
     */
    public static function isPowerOfTwo(?int $size): bool
    {
        return $size === null || ! ($size & ($size - 1));
    }
}
