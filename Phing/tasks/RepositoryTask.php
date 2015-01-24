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
use Composer\ComposerJson;
use Composer\ComposerLock;
use Repository\PackageRepositoryInterface;

/**
 * Class RepositoryTask
 *
 * @author   Javi H. Gil <https://github.com/javihgil>
 */
class RepositoryTask extends AbstractTask implements ActionTaskInterface
{

    /**
     * @var string
     */
    protected $jsonFile = 'composer.json';

    /**
     * @var string
     */
    protected $lockFile = 'composer.lock';

    /**
     * @var string
     */
    protected $localRepositoryDir = 'target/composer';

    /**
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $file;

    /**
     * @var string
     */
    protected $package;

    /**
     * @var string
     */
    protected $driver;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var bool
     */
    protected $sha1 = false;

    /**
     * @var string
     */
    protected $packageRegex;

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
     * @return string
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @param string $package
     *
     * @return $this
     */
    public function setPackage($package)
    {
        $this->package = $package;

        return $this;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     *
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
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
     * @param string $packageRegex
     * @return $this
     */
    public function setPackageRegex($packageRegex)
    {
        $this->packageRegex = $packageRegex;
        return $this;
    }

    /**
     * @param string $driver
     * @return $this
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * Main task
     */
    public function main()
    {
        if (!$this->testIf()) {
            return;
        }

        switch ($this->action) {
            case 'download-deps':
                $this->downloadDeps();
                break;

            case 'check-update':
                $this->checkUpdate();
                break;

            case 'upload-build':
                $this->upload('build');
                break;

            case 'upload-release':
                $this->upload('release');
                break;

            case 'download':
                $this->download();
                break;

            default:
                throw new \BuildException("$this->action action is not valid");
        }
    }

    /**
     * @return PackageRepositoryInterface
     * @throws BuildException
     */
    protected function getDriver()
    {
        $this->requireParam('driver');

        switch ($this->driver) {
            case 's3':
                $className = '\Repository\S3Repository';
                break;

            case 'local':
                $className = '\Repository\LocalRepository';
                break;

            default:
                $className = $this->driver;
        }

        if (!class_exists($className)) {
            throw new \BuildException("$className repository can not be found");
        }

        return $className::instance($this->getProject());
    }

    /**
     * @var array
     */
    protected $loadedRequirements = array();

    /**
     * Download private repository dependencies
     * @throws BuildException
     */
    public function downloadDeps()
    {
        $this->requireParam('jsonFile');
        $this->requireParam('localRepositoryDir');
        $this->requireParam('packageRegex');

        $this->log("Look for private dependencies in $this->jsonFile...", Project::MSG_INFO);

        $composerJson = ComposerJson::createFromFile($this->jsonFile);

        if (false !== $this->downloadJsonRequirements($composerJson)) {
            $composerJson->addRepository($this->localRepositoryDir);
            $composerJson->save($this->jsonFile);
        }
    }

    /**
     * @param string $package
     * @param string $packageGroup
     * @param string $name
     * @param string $version
     * @throws BuildException
     */
    protected function downloadFile($package, $packageGroup, $name, $version)
    {
        if (isset($this->loadedRequirements["$package-$version"])) {
            $this->log("Skip already processed dependency $package ($version)", Project::MSG_INFO);
            return;
        } else {
            $this->loadedRequirements["$package-$version"] = true;
        }

        $packageZipName = "$name-v$version.zip";

        $remoteFile = "$packageGroup/$packageZipName";
        $remoteSha1File = "$packageGroup/$packageZipName.sha1";
        $localFile = "$this->localRepositoryDir/$packageZipName";
        $localSha1File = "$this->localRepositoryDir/$packageZipName.sha1";

        $dw_start_time = microtime(true);
        if (\Composer\Version::isDev($version)) {
            if (!$this->getDriver()->buildExists($remoteFile)) {
                $this->log("Package not found in private repository", \Project::MSG_WARN);
                return;
            }

            $this->log("Download private build $remoteFile to $localFile");
            $this->getDriver()->downloadBuild($remoteFile, $localFile);

            $this->log("Download private build sha1 file $remoteSha1File to $localSha1File");
            $this->getDriver()->downloadBuild($remoteSha1File, $localSha1File);
        } else {
            if (!$this->getDriver()->releaseExists($remoteFile)) {
                $this->log("Package not found in private repository", \Project::MSG_WARN);
                return;
            }

            $this->log("Download private release $remoteFile to $localFile");
            $this->getDriver()->downloadRelease($remoteFile, $localFile);

            $this->log("Download private release sha1 file $remoteSha1File to $localSha1File");
            $this->getDriver()->downloadRelease($remoteSha1File, $localSha1File);
        }
        $dw_end_time = microtime(true);
        $dw_time_elapsed = round($dw_end_time - $dw_start_time, 3);

        $zipComposerJson = ComposerJson::createFromZip("$this->localRepositoryDir/$packageZipName");
        $this->downloadJsonRequirements($zipComposerJson);
    }


    /**
     * @param ComposerJson $composerJson
     * @return bool
     * @throws BuildException
     */
    protected function downloadJsonRequirements(ComposerJson $composerJson)
    {
        $requirements = $composerJson->getFilteredRequirements('/'.$this->packageRegex.'/i');

        if (!sizeof($requirements)) {
            $this->log("No dependencies found in ".$composerJson->getName(), Project::MSG_INFO);
            return false;
        } else {
            $this->log("Processing dependencies in ".$composerJson->getName(), Project::MSG_INFO);
            foreach ($requirements as $package => $version) {

                if ($version == 'self.version') {
                    $version = $composerJson->getVersion();
                }

                $packageGroup = \Composer\Package::group($package);
                $name = \Composer\Package::name($package);

                if (preg_match('/^~[0-9]+\.[0-9]+/', $version)) {
                    $this->log("Download all $package '$version' matching packages", Project::MSG_INFO);

                    $versionParts = explode('.', str_replace('~', '', $version));

                    // download all build files matching
                    $buildsList = $this->getDriver()->getPackageBuildList($packageGroup, $name);
                    foreach ($buildsList as $buildFile) {
                        $fileVersion = \Composer\Package::getVersionFromFilename($buildFile);

                        if (!version_compare("{$versionParts[0]}.{$versionParts[1]}", $fileVersion, 'le')) {
                            // echo "not download $fileVersion\n";
                            continue;
                        }

                        $nextVersion = $versionParts[0]+1;
                        if (!version_compare("{$nextVersion}.0", $fileVersion, 'gt')) {
                            // echo "not download $fileVersion\n";
                            continue;
                        }

                        $this->downloadFile($package, $packageGroup, $name, $fileVersion);
                    }

                    $releasesList = $this->getDriver()->getPackageReleaseList($packageGroup, $name);
                    foreach ($releasesList as $releaseFile) {
                        $fileVersion = \Composer\Package::getVersionFromFilename($releaseFile);

                        if (!version_compare("{$versionParts[0]}.{$versionParts[1]}", $fileVersion, 'le')) {
                            // echo "not download $fileVersion\n";
                            continue;
                        }

                        $nextVersion = $versionParts[0]+1;
                        if (!version_compare("{$nextVersion}.0", $fileVersion, 'gt')) {
                            // echo "not download $fileVersion\n";
                            continue;
                        }

                        $this->downloadFile($package, $packageGroup, $name, $fileVersion);
                    }

                } else {
                    // download normal version
                    $this->downloadFile($package, $packageGroup, $name, $version);
                }
            }
        }

        return true;
    }

    /**
     * @throws BuildException
     */
    public function checkUpdate()
    {
        $this->log("Checking private packages versions...", \Project::MSG_INFO);

        if (file_exists($this->lockFile)) {
            $composerLock = ComposerLock::createFromFile($this->lockFile);

            foreach ($composerLock->getAllPackages() as $package) {
                if (!$package->getDistUrl()) {
                    continue;
                }
                if (!file_exists($package->getDistUrl().'.sha1')) {
                    continue;
                }

                $shasum = $package->getDistShasum();
                $filesha1 = file_get_contents($package->getDistUrl().'.sha1');

                if ($shasum && $shasum != $filesha1) {
                    $this->log(
                        sprintf(
                            "Package %s (%s) has changes (lock: %s, remote: %s), updating composer.lock file with"
                            ." package's composer.json file values",
                            $package->getName(),
                            $package->getVersion(),
                            $shasum,
                            $filesha1
                        ),
                        Project::MSG_WARN
                    );

                    $zipComposerJson = ComposerJson::createFromZip($package->getDistUrl());
                    $zipComposerJson->setDistArray($package->getDistArray());
                    $zipComposerJson->setDistShasum($filesha1);

                    $composerLock->replacePackage($package, $zipComposerJson);
                    $composerLock->save($this->lockFile);
                } else {
                    $this->log(sprintf(
                        "Package %s (%s) has not changes (sha1: %s)",
                        $package->getName(),
                        $package->getVersion(),
                        $shasum
                    ), \Project::MSG_INFO);
                }
            }
        }
    }

    /**
     * @throws BuildException
     */
    public function download()
    {
        $this->requireParam('file');
        $this->requireParam('package');
        $this->requireParam('version');

        $name = $this->getPackage();
        $version = $this->getVersion();
        $format = 'tar.gz';

        $remoteFile = "$name-v$version.$format";
        $remoteSha1File = "$name-v$version.$format.sha1";
        $localFile = $this->file;
        $localSha1File = "{$this->file}.sha1";

        if (\Composer\Version::isDev($version)) {
            $this->log("Download private build $remoteFile to $localFile");
            $this->getDriver()->downloadBuild($remoteFile, $localFile);
            if ($this->sha1) {
                $this->log("Download private build sha1 file $remoteSha1File to $localSha1File");
                $this->getDriver()->downloadBuild($remoteSha1File, $localSha1File);
            }
        } else {
            $this->log("Download private release $remoteFile to $localFile");
            $this->getDriver()->downloadRelease($remoteFile, $localFile);
            if ($this->sha1) {
                $this->log("Download private release sha1 file $remoteSha1File to $localSha1File");
                $this->getDriver()->downloadRelease($remoteSha1File, $localSha1File);
            }
        }
    }

    /**
     * @param string $repo build|release
     * @throws BuildException
     */
    public function upload($repo)
    {
        $this->requireParam('jsonFile');
        $this->requireParam('file');

        $composerJson = ComposerJson::createFromFile($this->jsonFile);
        $name = $composerJson->getName();
        $version = $composerJson->getVersion();

        if (preg_match('/\.zip$/i', $this->file)) {
            $format = 'zip';
        } elseif (preg_match('/\.tar\.gz/i', $this->file)) {
            $format = 'tar.gz';
        } else {
            throw new \BuildException("File format is not valid ($this->file), allowed values are zip and tar.gz");
        }

        $remoteFile = "$name-v$version.$format";
        $remoteSha1File = "$name-v$version.$format.sha1";
        $localFile = $this->file;
        $localSha1File = "{$this->file}.sha1";

        switch ($repo) {
            case 'build':
                $this->log("Upload private build $localFile to $remoteFile");
                $this->getDriver()->uploadBuild($localFile, $remoteFile);

                if ($this->sha1) {
                    $this->log("Upload private build sha1 file $localSha1File to $remoteSha1File");
                    $this->getDriver()->uploadBuild($localSha1File, $remoteSha1File);
                }
                break;

            case 'release':
                $this->log("Upload private release $localFile to $remoteFile");
                $this->getDriver()->uploadRelease($localFile, $remoteFile);

                if ($this->sha1) {
                    $this->log("Upload private release sha1 file $localSha1File to $remoteSha1File");
                    $this->getDriver()->uploadRelease($localSha1File, $remoteSha1File);
                }
                break;
        }
    }
}
