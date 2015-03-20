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

use Console\Command;

/**
 * Class CommandTest
 *
 * @package Tests\Composer
 */
class CommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function execProvider()
    {
        return [
            ["echo 'test1'", true, ["test1\n", 0]],
            ["failedcommand 2>/dev/null", true, ["", 127]],
        ];
    }

    /**
     * @param string $execCommand
     * @param bool   $bufferResponse
     * @param array  $expectedResult
     *
     * @dataProvider execProvider
     */
    public function testExec($execCommand, $bufferResponse, $expectedResult)
    {
        $result = Command::exec($execCommand, $bufferResponse);
        $this->assertEquals($expectedResult, $result);
    }
}