<?php

/*
 * This file is part of the tools-project package.
 *
 * (c) Javi H. Gil <https://github.com/javihgil>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Repository;

use Console\Command;

/**
 * Class LocalRepository
 *
 * @package  Repository
 * @author   Javi H. Gil <https://github.com/javihgil>
 */
class LocalRepository implements PackageRepositoryInterface
{
    /**
     * @var LocalRepository
     */
    protected static $instance;

    /**
     * @param \Project $project
     * @return PackageRepositoryInterface
     */
    public static function instance(\Project $project)
    {
        if (!self::$instance) {
            self::$instance = new LocalRepository($project);
        }

        return self::$instance;
    }

    /**
     * @var \Project
     */
    protected $project;

    /**
     * @var string
     */
    protected $buildLocalPath;

    /**
     * @var string
     */
    protected $releaseLocalPath;

    /**
     * @param \Project $project
     */
    private function __construct(\Project $project)
    {
        $this->project = $project;
        $this->buildLocalPath = $this->project->getProperty('repository.local.build.path');
        $this->releaseLocalPath = $this->project->getProperty('repository.local.release.path');
    }

    /**
     * @param string $remoteFile
     * @return bool
     */
    public function buildExists($remoteFile)
    {
        return file_exists("$this->buildLocalPath/$remoteFile");
    }

    /**
     * @param string $remoteFile
     * @param string $targetFile
     * @return bool
     */
    public function downloadBuild($remoteFile, $targetFile)
    {
        return copy("$this->buildLocalPath/$remoteFile", $targetFile);
    }

    /**
     * @param string $sourceFile
     * @param string $remoteFile
     * @return bool
     */
    public function uploadBuild($sourceFile, $remoteFile)
    {
        return copy($sourceFile, "$this->buildLocalPath/$remoteFile");
    }

    /**
     * @param string $path
     * @param string $name
     * @return array
     */
    public function getPackageBuildList($path, $name = '')
    {
        throw new \Exception("getPackageReleaseList not yet implemented in LocalRepository");
    }

    /**
     * @param string $remoteFile
     * @return bool
     */
    public function releaseExists($remoteFile)
    {
        return file_exists("$this->releaseLocalPath/$remoteFile");
    }

    /**
     * @param string $remoteFile
     * @param string $targetFile
     * @return bool
     */
    public function downloadRelease($remoteFile, $targetFile)
    {
        return copy("$this->releaseLocalPath/$remoteFile", $targetFile);
    }

    /**
     * @param string $sourceFile
     * @param string $remoteFile
     * @return bool
     */
    public function uploadRelease($sourceFile, $remoteFile)
    {
        return copy($sourceFile, "$this->releaseLocalPath/$remoteFile");
    }

    /**
     * @param string $path
     * @param string $name
     * @return array
     */
    public function getPackageReleaseList($path, $name = '')
    {
        throw new \Exception("getPackageReleaseList not yet implemented in LocalRepository");
    }
}
