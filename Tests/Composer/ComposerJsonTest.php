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

use Composer\ComposerJson;

/**
 * Class ComposerJsonTest
 *
 * @package Tests\Composer
 */
class ComposerJsonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests construct
     */
    public function testConstructFailed()
    {
        $this->setExpectedException('BuildException');
        new ComposerJson('');
    }

    /**
     * @return array
     */
    public function composerJsonProvider()
    {
        $composerJsonString1 = '{
    "name": "test1",
    "version": "1.0.0",
    "type": "project",
    "require": {
        "test/dependency1": "1.0.0",
        "test/dependency2": "1.0.0"
    },
    "require-dev": {
        "test/dependency3": "1.0.0",
        "test/dependency4": "1.0.0"
    }
}';
        $composerJsonString2 = '{
    "version": "0.0.1-dev",
    "require": {}
}';

        return [
            [
                $composerJsonString1,
                [
                    'require' => ['test/dependency1'=>'1.0.0','test/dependency2'=>'1.0.0'],
                    'require-dev' => ['test/dependency3'=>'1.0.0','test/dependency4'=>'1.0.0'],
                    'name' => 'test1',
                    'version' => '1.0.0',
                    'type' => 'project',
                ]
            ],
            [
                $composerJsonString2,
                [
                    'require' => [],
                    'require-dev' => [],
                    'name' => '',
                    'version' => '0.0.1-dev',
                    'type' => '',
                ]
            ],
        ];
    }

    /**
     * @param string $composerJsonString
     * @param array  $expectedData
     *
     * @dataProvider composerJsonProvider
     */
    public function testRequires($composerJsonString, $expectedData)
    {
        $composerJson = new ComposerJson($composerJsonString);
        $requirements = $composerJson->getRequires();
        $this->assertEquals(sizeof($expectedData['require']), sizeof($requirements));

        foreach ($expectedData['require'] as $package => $version) {
            $this->assertArrayHasKey($package, $requirements);
            $this->assertEquals($version, $requirements[$package]);
        }
    }

    /**
     * Tests setRequires
     */
    public function testSetRequires()
    {
        $requires = ['test/require1'=>'1.0.0'];

        $composerJson = new ComposerJson('{"name":"test"}');
        $composerJson->setRequires($requires);
        $this->assertEquals($requires, $composerJson->getRequires());
    }

    /**
     * @param string $composerJsonString
     * @param array  $expectedData
     *
     * @dataProvider composerJsonProvider
     */
    public function testDevRequires($composerJsonString, $expectedData)
    {
        $composerJson = new ComposerJson($composerJsonString);
        $requirements = $composerJson->getRequiresDev();
        $this->assertEquals(sizeof($expectedData['require-dev']), sizeof($requirements));

        foreach ($expectedData['require-dev'] as $package => $version) {
            $this->assertArrayHasKey($package, $requirements);
            $this->assertEquals($version, $requirements[$package]);
        }
    }

    /**
     * Tests setDevRequres
     */
    public function testSetDevRequires()
    {
        $requires = ['test/require1'=>'1.0.0'];

        $composerJson = new ComposerJson('{"name":"test"}');
        $composerJson->setRequiresDev($requires);
        $this->assertEquals($requires, $composerJson->getRequiresDev());
    }

    /**
     * @param string $composerJsonString
     * @param array  $expectedData
     *
     * @dataProvider composerJsonProvider
     */
    public function testGetName($composerJsonString, $expectedData)
    {
        $composerJson = new ComposerJson($composerJsonString);
        $this->assertEquals($expectedData['name'], $composerJson->getName());
    }

    /**
     * @param string $composerJsonString
     * @param array  $expectedData
     *
     * @dataProvider composerJsonProvider
     */
    public function testGetVersion($composerJsonString, $expectedData)
    {
        $composerJson = new ComposerJson($composerJsonString);
        $this->assertEquals($expectedData['version'], $composerJson->getVersion());
    }
}