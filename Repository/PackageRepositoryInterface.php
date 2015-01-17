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

/**
 * Interface PackageRepositoryInterface
 *
 * @package  Repository
 * @author   Javi H. Gil <https://github.com/javihgil>
 */
interface PackageRepositoryInterface
{
    /**
     * @param \Project $project
     * @return PackageRepositoryInterface
     */
    public static function instance(\Project $project);

    /**
     * @param string $remoteFile
     * @return bool
     */
    public function buildExists($remoteFile);

    /**
     * @param string $remoteFile
     * @param string $targetFile
     * @return bool
     */
    public function downloadBuild($remoteFile, $targetFile);

    /**
     * @param string $sourceFile
     * @param string $remoteFile
     * @return bool
     */
    public function uploadBuild($sourceFile, $remoteFile);

    /**
     * @param string $remoteFile
     * @return bool
     */
    public function releaseExists($remoteFile);

    /**
     * @param string $remoteFile
     * @param string $targetFile
     * @return bool
     */
    public function downloadRelease($remoteFile, $targetFile);

    /**
     * @param string $sourceFile
     * @param string $remoteFile
     * @return bool
     */
    public function uploadRelease($sourceFile, $remoteFile);
}
