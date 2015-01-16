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
 * Class PackageTask
 *
 * @author Javi H. Gil <https://github.com/javihgil>
 */
class PackageTask extends AbstractTask
{

    /**
     * @var string
     */
    protected $format;

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
    protected $log;

    /**
     * @var bool
     */
    protected $sha1 = false;

    /**
     * @var bool
     */
    protected $md5 = false;

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
     * @param string $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $log
     */
    public function setLog($log)
    {
        $this->log = $log;
    }

    /**
     * @return string
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @param boolean $md5
     */
    public function setMd5($md5)
    {
        $this->md5 = $md5;
    }

    /**
     * @return boolean
     */
    public function getMd5()
    {
        return $this->md5;
    }

    /**
     * @param boolean $sha1
     */
    public function setSha1($sha1)
    {
        $this->sha1 = $sha1;
    }

    /**
     * @return boolean
     */
    public function getSha1()
    {
        return $this->sha1;
    }

    /**
     * Fails if final result is marked as failed
     */
    public function main()
    {
        if ($this->log) {
            $log = "> ../$this->log";
        } else {
            $log = "> /dev/null";
        }

        switch ($this->format) {
            case 'zip':
                $this->exec(
                    "Package code from $this->dir to $this->file",
                    "cd $this->dir ; zip ../$this->file * -r $log"
                );
                break;

            case 'tgz':
                $this->exec(
                    "Package code from $this->dir to $this->file",
                    "cd $this->dir ; tar -czvf ../$this->file * $log"
                );
                break;

            default:
                throw new \BuildException("Format $this->format is not valid (zip,tgz)");
        }

        if ($this->md5) {
            $md5 = md5_file("$this->dir/../$this->file");
            file_put_contents("$this->dir/../$this->file.md5", $md5);
            $this->log("Create md5 for $this->dir/../$this->file in $this->dir/../$this->file.md5", \Project::MSG_INFO);
        }

        if ($this->sha1) {
            $sha1 = sha1_file("$this->dir/../$this->file");
            file_put_contents("$this->dir/../$this->file.sha1", $sha1);
            $this->log(
                "Create sha1 for $this->dir/../$this->file in $this->dir/../$this->file.sha1",
                \Project::MSG_INFO
            );
        }
    }
}
