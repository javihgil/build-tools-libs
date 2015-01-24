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
 * Class TestTask
 *
 * @author Javi H. Gil <https://github.com/javihgil>
 */
class TestTask extends AbstractTask
{

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $bin;

    /**
     * @var string
     */
    protected $config;

    /**
     * @var array
     */
    protected $filesets = array();

    /**
     * Nested creator, adds a set of files (nested <fileset> attribute).
     * This is for when you don't care what order files get appended.
     *
     * @return FileSet
     */
    public function createFileSet()
    {
        $num = array_push($this->filesets, new FileSet());
        return $this->filesets[$num - 1];
    }


    /**
     * @var boolean
     */
    protected $failbuild = true;

    /**
     * @var string
     */
    protected $coverageHtmlTarget;

    /**
     * @var string
     */
    protected $coverageCloverTarget;

    /**
     * @var string
     */
    protected $reportTarget;

    /**
     * @var string
     */
    protected $standard;

    /**
     * @param string $bin
     */
    public function setBin($bin)
    {
        $this->bin = $bin;
    }

    /**
     * @param string $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @param boolean $failbuild
     */
    public function setFailbuild($failbuild)
    {
        $this->failbuild = $failbuild;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @param string $target
     */
    public function setCoverageHtmlTarget($target)
    {
        $this->coverageHtmlTarget = $target;
    }

    /**
     * @param string $coverageCloverTarget
     */
    public function setCoverageCloverTarget($coverageCloverTarget)
    {
        $this->coverageCloverTarget = $coverageCloverTarget;
    }

    /**
     * @param string $reportTarget
     *
     * @return $this
     */
    public function setReportTarget($reportTarget)
    {
        $this->reportTarget = $reportTarget;

        return $this;
    }


    /**
     * @var string
     */
    protected $excludes;

    /**
     * @param string $excludes
     */
    public function setExcludes($excludes)
    {
        $this->excludes = $excludes;
    }

    /**
     * @param string $standard
     *
     * @return $this
     */
    public function setStandard($standard)
    {
        $this->standard = $standard;
        return $this;
    }

    /**
     * @throws BuildException
     */
    public function main()
    {
        if ($this->testIf()) {
            switch ($this->method) {
                case 'phpunit':
                    $this->phpunit();
                    break;

                case 'phplint':
                    $this->phplint();
                    break;

                case 'twiglint':
                    $this->twiglint();
                    break;

                case 'phploc':
                    $this->phploc();
                    break;

                case 'phpcs':
                    $this->phpcs();
                    break;

                default:
                    throw new \BuildException("'$this->method' action is not defined in TestTask");
            }
        }
    }

    /**
     * @throws BuildException
     */
    public function phpunit()
    {
        $this->requireParam('bin');
        $this->requireParam('config');

        $coverage = $this->coverageHtmlTarget ? "--coverage-html {$this->coverageHtmlTarget} --coverage-clover {$this->coverageCloverTarget}" : '';

        $result = $this->exec("Phpunit", "php $this->bin -c $this->config $coverage", [], \Project::MSG_INFO, true, false);

        if ($this->failbuild && (bool)$result) {
            $this->log("Phpunit returns $result", \Project::MSG_ERR);
            throw new \BuildException("Phpunit tests failed");
        }
    }

    /**
     * @throws BuildException
     */
    public function phpcs()
    {
        $this->requireParam('bin');
        $this->requireParam('reportTarget');
        $this->requireParam('excludes');
        $this->requireParam('standard');

        $this->exec(
            "PhpCS",
            "php $this->bin --report=checkstyle --report-file=$this->reportTarget --standard=$this->standard --ignore=$this->excludes .",
            [],
            \Project::MSG_INFO,
            true,
            false
        );
    }

    /**
     * @throws BuildException
     */
    public function phploc()
    {
        $this->requireParam('bin');

        $options = array();

        if ($this->excludes) {
            $excludes = explode(',', $this->excludes);
            foreach ($excludes as $exclude) {
                $options[] = "--exclude '$exclude'";
            }
        }

        $result = $this->exec("Phploc", "php $this->bin --log-csv $this->reportTarget .", $options, \Project::MSG_INFO, true, false);

        if ($this->failbuild && (bool)$result) {
            $this->log("Phploc returns $result", \Project::MSG_ERR);
            throw new \BuildException("Phploc report failed");
        }
    }

    /**
     * @throws BuildException
     */
    public function phplint()
    {
        $this->log('Phplint', \Project::MSG_INFO);

        if (!sizeof($this->filesets)) {
            throw new \BuildException('Phplint requires a fileset');
        }

        $failbuild = false;

        foreach ($this->filesets as $fs) {
            $files = $fs->getDirectoryScanner($this->project)->getIncludedFiles();

            foreach ($files as $file) {
                $result = $this->exec('', "php -l $file", array(), \Project::MSG_DEBUG, true, false);
                $failbuild |= (bool)$result;
            }
        }
        unset($this->filesets);

        if ($this->failbuild && (bool)$failbuild) {
            $this->log("PHPlint returns $result", \Project::MSG_ERR);
            throw new \BuildException("PHPlint tests failed");
        }
    }

    /**
     * @throws BuildException
     */
    public function twiglint()
    {
        $this->requireParam('bin');
        $this->log('Twiglint', \Project::MSG_INFO);

        if (!sizeof($this->filesets)) {
            throw new \BuildException('Twiglint requires a fileset');
        }

        $failbuild = false;

        foreach ($this->filesets as $fs) {
            $files = $fs->getDirectoryScanner($this->project)->getIncludedFiles();

            foreach ($files as $file) {
                $result = $this->exec('', "php $this->bin lint $file", array(), \Project::MSG_DEBUG, true, false);
                $failbuild |= (bool)$result;
            }
        }
        unset($this->filesets);

        if ($this->failbuild && (bool)$failbuild) {
            $this->log("Twiglint returns 1", \Project::MSG_ERR);
            throw new \BuildException("Twiglint tests failed");
        }
    }
}
