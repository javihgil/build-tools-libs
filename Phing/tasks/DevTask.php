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
use Task\ActionTaskInterface;

/**
 * Class DevTask
 *
 * @author Javi H. Gil <https://github.com/javihgil>
 */
class DevTask extends AbstractTask implements ActionTaskInterface
{

    /**
     * Inits task
     */
    public function init()
    {
        $this->jsonFile = 'composer.json';
    }

    /**
     * @var string
     */
    protected $action;

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @var string
     */
    protected $jsonFile;

    /**
     * @param string $jsonFile
     */
    public function setJsonFile($jsonFile)
    {
        $this->jsonFile = $jsonFile;
    }

    /**
     * @return string
     */
    public function getJsonFile()
    {
        return $this->jsonFile;
    }

    /**
     * TODO Rename vendorDir to "target"
     * @var string
     */
    protected $vendorDir;

    /**
     * TODO Rename localDir to "origin"
     * @var string
     */
    protected $localDir;

    /**
     * @param string $localDir
     */
    public function setLocalDir($localDir)
    {
        $this->localDir = $localDir;
    }

    /**
     * @return string
     */
    public function getLocalDir()
    {
        return $this->localDir;
    }

    /**
     * @param string $vendorDir
     */
    public function setVendorDir($vendorDir)
    {
        $this->vendorDir = $vendorDir;
    }

    /**
     * @return string
     */
    public function getVendorDir()
    {
        return $this->vendorDir;
    }

    /**
     * @throws BuildException
     */
    public function main()
    {
        switch ($this->action) {
            case 'create-symlink':
                $this->createSymlink();
                break;

            case 'remove-symlink':
                $this->removeSymlink();
                break;

            default:
                throw new \BuildException("$this->action action is not valid");
        }
    }

    /**
     * @return bool
     * @throws BuildException
     */
    protected function createSymlink()
    {
        $this->requireParam('vendorDir');
        $this->requireParam('localDir');

        if (file_exists($this->vendorDir) && is_link($this->vendorDir)) {
            $this->log("$this->vendorDir link already exists", \Project::MSG_INFO);
            return false;
        }

        if (strpos($this->localDir, '/') === 0) /* absolute path */ {
            if (!is_dir("$this->localDir")) {
                $this->log("$this->localDir directory does not exist", \Project::MSG_WARN);
                return false;
            }
        }

        if (file_exists($this->vendorDir) && is_dir($this->vendorDir)) {
            // remove dir
            $this->log("Remove dir $this->vendorDir", \Project::MSG_INFO);
            self::rmRf($this->vendorDir);
        }

        // create symlink
        $this->log("Create link $this->vendorDir to $this->localDir", \Project::MSG_INFO);
        symlink($this->localDir, $this->vendorDir);

        return true;
    }

    /**
     * @return bool
     * @throws BuildException
     */
    protected function removeSymlink()
    {
        $this->requireParam('vendorDir');

        if (!file_exists($this->vendorDir)) {
            $this->log("$this->vendorDir does not exist", \Project::MSG_INFO);
            return false;
        }

        if (!is_link($this->vendorDir)) {
            $this->log("$this->vendorDir is not a symlink", \Project::MSG_INFO);
            return false;
        }

        $this->log("Remove symlink $this->vendorDir", \Project::MSG_INFO);
        unlink($this->vendorDir);

        return true;
    }

    /**
     * @param string $path
     */
    protected static function rmRf($path)
    {
        if (!file_exists($path)) {
            return;
        }

        $paths = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($paths as $subpath) {
            $subpath->isFile() ? unlink($subpath->getPathname()) : rmdir($subpath->getPathname());
        }
        rmdir($path);
    }
}
