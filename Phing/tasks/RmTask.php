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
 * Class RmTask
 *
 * @author Javi H. Gil <https://github.com/javihgil>
 */
class RmTask extends AbstractTask
{

    /**
     * @var string
     */
    protected $dir;

    /**
     * @var string
     */
    protected $file;

    /**
     * @var string
     */
    protected $pattern;

    /**
     * @param string $dir
     */
    public function setDir($dir)
    {
        $this->dir = $dir;
    }

    /**
     * @return string
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     *
     */
    public function main()
    {
        if ($this->dir) {
            if (is_dir($this->dir)) {
                $this->exec("Remove $this->dir directory", "rm -Rf $this->dir");
            } else {
                $this->log("$this->dir directory does not exist in ".getcwd(), \Project::MSG_WARN);
            }
        }

        if ($this->file) {
            if (file_exists($this->file)) {
                $this->exec("Remove $this->file file", "rm -f $this->file");
            } else {
                $this->log("$this->file file does not exist in ".getcwd(), \Project::MSG_WARN);
            }
        }

        if ($this->pattern) {
            $this->exec("Remove $this->pattern", "rm -Rf $this->pattern");
        }
    }
}
