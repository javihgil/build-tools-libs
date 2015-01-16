<?php

/*
 * This file is part of the build-tools-lib package.
 *
 * (c) Javi H. Gil <https://github.com/javihgil>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Task;

/**
 * Interface ActionTaskInterface
 *
 * @package  Task
 * @author   Javi H. Gil <https://github.com/javihgil>
 */
interface ActionTaskInterface
{
    /**
     * @return string
     */
    public function getAction();

    /**
     * @param string $action
     */
    public function setAction($action);
}
