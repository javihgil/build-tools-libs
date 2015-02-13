<?php

/*
 * This file is part of the deploy package.
 *
 * (c) Javi H. Gil <https://github.com/javihgil>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once "lib/autoload.php";

use Task\AbstractTask;

/**
 * Class ExitTask
 *
 * @package  Phing\tasks
 * @author   Javi H. Gil <https://github.com/javihgil>
 */
class ExitTask extends AbstractTask
{
    protected $message;
    protected $level = 'info';

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param mixed $level
     * @return $this
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

   /**
    * Exits
    */
    public function main()
    {
        if (!$this->testIf() || !$this->testUnless()) {
            return;
        }

        $levels = [
            'error' => \Project::MSG_ERR,
            'warning' => \Project::MSG_WARN,
            'info' => \Project::MSG_INFO,
        ];

        if ($this->message) {
            $this->log($this->message, $levels[$this->level]);
        }

        exit();
    }
}
