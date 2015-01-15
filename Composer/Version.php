<?php

/*
 * This file is part of the build-tools-lib package.
 *
 * (c) Javi H. Gil <https://github.com/javihgil>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer;

/**
 * Class Version
 *
 * @package Composer
 * @author  Javi H. Gil <https://github.com/javihgil>
 */
class Version
{

    /**
     * @param $version
     *
     * @return int
     */
    public static function isDev($version)
    {
        return preg_match('/\-dev$/i', $version);
    }

    /**
     * @param $version
     *
     * @return string
     */
    public static function dev($version)
    {
        if (!self::isDev($version)) {
            $version .= '-dev';
        }
        return $version;
    }

    /**
     * @param $version
     *
     * @return mixed
     */
    public static function release($version)
    {
        return str_ireplace(array('-dev'), '', $version);
    }
}