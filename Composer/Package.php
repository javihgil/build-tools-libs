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
        return explode('/', $packageName, 2);
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

    /**
     * @param string $package
     *
     * @return string|null
     *
     * @example "package-build-file-v1.0.0-dev.tar.gz" => "1.0.0-dev"
     * @example "package-release-file-v1.0.0.tar.gz" => "1.0.0"
     */
    public static function getVersionFromFilename($package)
    {
        if (preg_match('/v([0-9]+.[0-9]+.[0-9]+(\-dev)?)/', $package, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
