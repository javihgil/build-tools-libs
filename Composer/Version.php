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
 */
class Version
{
    /**
     * @param string $version
     *
     * @return int
     *
     * @example "1.0.0-dev" => true
     * @example "1.0.0" => false
     */
    public static function isDev($version)
    {
        return (bool) preg_match('/\-dev$/i', $version);
    }

    /**
     * Returns development versions
     *
     * @param string $version
     *
     * @return string
     *
     * @example "1.0.0-dev" => "1.0.0-dev"
     * @example "1.0.0" => "1.0.0-dev"
     */
    public static function dev($version)
    {
        if (!self::isDev($version)) {
            $version .= '-dev';
        }

        return $version;
    }

    /**
     * Returns release versions
     *
     * @param string $version
     *
     * @return string
     *
     * @example "1.0.0-dev" => "1.0.0"
     * @example "1.0.0" => "1.0.0"
     */
    public static function release($version)
    {
        return str_ireplace(array('-dev'), '', $version);
    }
}
