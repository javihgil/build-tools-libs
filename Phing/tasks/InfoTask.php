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

use Task\AbstractTask;

/**
 * Class InfoTask
 *
 * @author Javi Hernández <javihgil@gmail.com>
 */
class InfoTask extends AbstractTask
{

    /**
     * @var string
     */
    protected $show;

    /**
     * @var string
     */
    protected $property;

    /**
     * @param string $show
     */
    public function setShow($show)
    {
        $this->show = $show;
    }

    /**
     * @return string
     */
    public function getShow()
    {
        return $this->show;
    }

    /**
     * @param string $property
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     *
     */
    public function main()
    {
        switch ($this->show) {
            case 'target':
                preg_match('/.*\/tools\/phing\/projects\/(.+)\.xml\:[0-9]+\:[0-9]+/i', $this->getLocation(), $matches);
                $file = str_ireplace('/', '-', $matches[1]);
                $task = $this->getOwningTarget()->getName();
                $this->log("Target $file:$task", \Project::MSG_INFO);
                break;

            case 'property':
                $value = $this->getProject()->getProperty($this->getProperty());
                $this->log("Property $this->property: $value", \Project::MSG_INFO);
                break;
        }
    }
}
