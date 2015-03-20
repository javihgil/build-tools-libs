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

use Composer\Package;

/**
 * Class PackageTest
 * 
 * @package Tests\Composer
 */
class PackageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function packageNameProvider()
    {
        return [
            ['vendor1/package1',['vendor1','package1']],
            ['vendor1/package2-with-dashes',['vendor1','package2-with-dashes']],
            ['vendor2-with-dashes/package2-with-dashes',['vendor2-with-dashes','package2-with-dashes']],
            ['vendor1/package3/with-subpackages1',['vendor1','package3/with-subpackages1']],
        ];
    }

    /**
     * @param string $packageName
     * @param array  $expectedData
     *
     * @dataProvider packageNameProvider
     */
    public function testExplodePackageName($packageName, $expectedData)
    {
        $result = Package::explodePackageName($packageName);

        $this->assertEquals($expectedData[0], $result[0]);
        $this->assertEquals($expectedData[1], $result[1]);
    }

    /**
     * @param string $packageName
     * @param array  $expectedData
     *
     * @dataProvider packageNameProvider
     */
    public function testGroup($packageName, $expectedData)
    {
        $result = Package::group($packageName);
        $this->assertEquals($expectedData[0], $result);
    }

    /**
     * @param string $packageName
     * @param array  $expectedData
     *
     * @dataProvider packageNameProvider
     */
    public function testName($packageName, $expectedData)
    {
        $result = Package::name($packageName);
        $this->assertEquals($expectedData[1], $result);
    }

    /**
     * @return array
     */
    public function filenameProvider()
    {
        return [
            ['package1-v1.0.0-dev.tar.gz', '1.0.0-dev'],
            ['package2-v1.0.0.tar.gz', '1.0.0'],
            ['package3-v1.0.0.zip', '1.0.0'],
            ['invalid.zip', null],
        ];
    }

    /**
     * @param string $packageFilename
     * @param string $expectedVersion
     *
     * @dataProvider filenameProvider
     */
    public function testGetVersionFromFilename($packageFilename, $expectedVersion)
    {
        $result = Package::getVersionFromFilename($packageFilename);
        $this->assertEquals($expectedVersion, $result);
    }
}