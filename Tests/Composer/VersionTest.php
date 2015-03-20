<?php

/*
 * This file is part of the build-tools-lib package.
 *
 * (c) Javi H. Gil <https://github.com/javihgil>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Composer;

use Composer\Version;

/**
 * Class VersionTest
 * 
 * @package Tests\Composer
 */
class VersionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests isDev
     */
    public function testIsDev()
    {
        $this->assertFalse(Version::isDev('1.0.0'));
        $this->assertTrue(Version::isDev('1.0.0-dev'));
    }

    /**
     * Tests dev
     */
    public function testDev()
    {
        $this->assertEquals('1.0.0-dev', Version::dev('1.0.0'));
        $this->assertEquals('1.0.0-dev', Version::dev('1.0.0-dev'));
    }

    /**
     * Tests release
     */
    public function testRelease()
    {
        $this->assertEquals('1.0.0', Version::release('1.0.0'));
        $this->assertEquals('1.0.0', Version::release('1.0.0-dev'));
    }
}