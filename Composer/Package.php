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
 * Class Package
 *
 * @package Composer
 * @author  Javi H. Gil <https://github.com/javihgil>
 */
class Package
{
    /**
     * @param string $packageName
     *
     * @return array
     */
    public static function explodePackageName($packageName)
    {
        return explode('/', $packageName);
    }

    /**
     * @param string $packageName
     *
     * @return string
     */
    public static function group($packageName)
    {
        $data = self::explodePackageName($packageName);
        return $data[0];
    }

    /**
     * @param string $packageName
     *
     * @return string
     */
    public static function name($packageName)
    {
        $data = self::explodePackageName($packageName);
        return $data[1];
    }


    public static function getVersionFromFilename($package)
    {
        if (preg_match('/v([0-9]+.[0-9]+.[0-9]+(\-dev)?)/', $package, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
