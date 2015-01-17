<?php

/*
 * This file is part of the tools-project package.
 *
 * (c) Javi H. Gil <https://github.com/javihgil>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Console;

/**
 * Class Command
 *
 * @package  Console
 * @author   Javi H. Gil <https://github.com/javihgil>
 */
class Command
{
    /**
     * @param string $execCommand
     * @return array [returnedString, result]
     */
    public static function exec($execCommand)
    {
        ob_start();
        $returnedString = system($execCommand, $result);
        ob_end_clean();

        return array($returnedString, $result);
    }
}
