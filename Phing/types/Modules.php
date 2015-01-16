<?php

/*
 * This file is part of the build-tools-lib package.
 *
 * (c) Javi H. Gil <https://github.com/javihgil>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once "lib/autoload.php";

/**
 * Class Modules
 *
 * @author  Javi H. Gil <https://github.com/javihgil>
 */
class Modules extends \FileSet
{
    /**
     * @var array
     */
    protected $modules = array();

    /**
     * @return Module
     * @throws BuildException
     */
    public function createModule()
    {
        if ($this->isReference()) {
            throw $this->noChildrenAllowed();
        }
        return $this->modules[] = new Module();
    }

    /**
     * @return array
     */
    public function getModules()
    {
        return $this->modules;
    }
}
