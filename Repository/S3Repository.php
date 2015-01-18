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
 * Class S3Repository
 *
 * @package  Repository
 * @author   Javi H. Gil <https://github.com/javihgil>
 */
class S3Repository implements PackageRepositoryInterface
{
    /**
     * @var S3Repository
     */
    protected static $instance;

    /**
     * @param \Project $project
     * @return PackageRepositoryInterface
     */
    public static function instance(\Project $project)
    {
        if (!self::$instance) {
            self::$instance = new S3Repository($project);
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
    protected $buildBucketPath;

    /**
     * @var string
     */
    protected $releaseBucketPath;

    /**
     * @var string
     */
    protected $s3cmdBin = '/usr/bin/s3cmd';

    /**
     * @param \Project $project
     */
    private function __construct(\Project $project)
    {
        $this->project = $project;
        $this->buildBucketPath = $this->project->getProperty('s3.build.bucket.path');
        $this->releaseBucketPath = $this->project->getProperty('s3.release.bucket.path');

        if (!$this->buildBucketPath) {
            throw new \BuildException('s3.build.bucket.path property must not be empty');
        }

        if (!$this->releaseBucketPath) {
            throw new \BuildException('s3.release.bucket.path property must not be empty');
        }

        if ($this->project->getProperty('s3.cmd.bin')) {
            $this->s3cmdBin = $this->project->getProperty('s3.cmd.bin');
        }

        $this->checkCommand();
    }

    /**
     * @throws \Exception
     */
    protected function checkCommand()
    {
        list($returnedString, $result) = Command::exec("ls $this->s3cmdBin 2>/dev/null | wc -l");

        if (!(bool)$returnedString) {
            throw new \Exception("s3cmd command not available at $this->s3cmdBin.");
        }
    }

    /**
     * @param string $remoteFile
     * @return bool
     */
    public function buildExists($remoteFile)
    {
        list($returnedString, $result) = Command::exec("s3cmd ls s3://$this->buildBucketPath/$remoteFile | wc -l");

        return (bool)$returnedString;
    }

    /**
     * @param string $remoteFile
     * @param string $targetFile
     * @return bool
     */
    public function downloadBuild($remoteFile, $targetFile)
    {
        list($returnedString, $result) = Command::exec(
            "s3cmd get s3://$this->buildBucketPath/$remoteFile $targetFile --force"
        );

        return (bool)$returnedString;
    }

    /**
     * @param string $sourceFile
     * @param string $remoteFile
     * @return bool
     */
    public function uploadBuild($sourceFile, $remoteFile)
    {
        list($returnedString, $result) = Command::exec(
            "s3cmd put $sourceFile s3://$this->buildBucketPath/$remoteFile"
        );

        return (bool)$returnedString;
    }

    /**
     * @param string $remoteFile
     * @return bool
     */
    public function releaseExists($remoteFile)
    {
        list($returnedString, $result) = Command::exec("s3cmd ls s3://$this->releaseBucketPath/$remoteFile | wc -l");

        return (bool)$returnedString;
    }

    /**
     * @param string $remoteFile
     * @param string $targetFile
     * @return bool
     */
    public function downloadRelease($remoteFile, $targetFile)
    {
        list($returnedString, $result) = Command::exec(
            "s3cmd get s3://$this->releaseBucketPath/$remoteFile $targetFile --skip-existing"
        );

        return (bool)$returnedString;
    }

    /**
     * @param string $sourceFile
     * @param string $remoteFile
     * @return bool
     */
    public function uploadRelease($sourceFile, $remoteFile)
    {
        list($returnedString, $result) = Command::exec(
            "s3cmd put $sourceFile s3://$this->releaseBucketPath/$remoteFile"
        );

        return (bool)$returnedString;
    }
}
