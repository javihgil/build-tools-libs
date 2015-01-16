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
 * Class CodeCopyTask
 *
 * @author Javi H. Gil <https://github.com/javihgil>
 */
class CodeCopyTask extends AbstractTask
{
    /**
     * @var string
     */
    protected $from;

    /**
     * @var string
     */
    protected $to;

    /**
     * @var string
     */
    protected $logFile;

    /**
     * @var string
     */
    protected $excludes;

    /**
     * @var bool
     */
    protected $stats = false;

    /**
     * @param string $excludes
     */
    public function setExcludes($excludes)
    {
        $this->excludes = $excludes;
    }

    /**
     * @return string
     */
    public function getExcludes()
    {
        return $this->excludes;
    }

    /**
     * @param string $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string $logFile
     */
    public function setLogFile($logFile)
    {
        $this->logFile = $logFile;
    }

    /**
     * @return string
     */
    public function getLogFile()
    {
        return $this->logFile;
    }

    /**
     * @param string $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param boolean $stats
     */
    public function setStats($stats)
    {
        $this->stats = $stats;
    }

    /**
     * @return boolean
     */
    public function getStats()
    {
        return $this->stats;
    }

    /**
     *
     */
    public function main()
    {
        $options = array();

        if ($this->logFile) {
            $options[] = "--log-file=$this->logFile";
        }

        if ($this->stats) {
            $options[] = "--stats";
        }

        if ($this->excludes) {
            $excludes = explode(',', $this->excludes);
            foreach ($excludes as $exclude) {
                $options[] = "--exclude '$exclude'";
            }
        }

        $this->exec("Copy code from $this->from to $this->to", "rsync -h -a $this->from $this->to", $options);
    }
}
