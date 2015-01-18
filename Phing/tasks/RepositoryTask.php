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

        $composerJson = new ComposerJson($this->jsonFile);

        if (false !== $this->downloadJsonRequirements($composerJson)) {
            $composerJson->addRepository($this->localRepositoryDir);
            $composerJson->save($this->jsonFile);
        }
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

                if (preg_match('/^~.*/', $version)) {
                    throw new \BuildException("Look for ~ version feature not yet implemented");
                }

                if (isset($this->loadedRequirements["$package-$version"])) {
                    $this->log("Skip already processed dependency $package ($version)", Project::MSG_INFO);
                    continue;
                } else {
                    $this->loadedRequirements["$package-$version"] = true;
                }

                $packageGroup = \Composer\Package::group($package);
                $name = \Composer\Package::name($package);
                $packageZipName = "$name-v$version.zip";

                $remoteFile = "$packageGroup/$packageZipName";
                $remoteSha1File = "$packageGroup/$packageZipName.sha1";
                $localFile = "$this->localRepositoryDir/$packageZipName";
                $localSha1File = "$this->localRepositoryDir/$packageZipName.sha1";

                $dw_start_time = microtime(true);
                if (\Composer\Version::isDev($version)) {
                    if (!$this->getDriver()->buildExists($remoteFile)) {
                        $this->log("Package not found in private repository", \Project::MSG_WARN);
                        continue;
                    }

                    $this->getDriver()->downloadBuild($remoteFile, $localFile);
                    $this->getDriver()->downloadBuild($remoteSha1File, $localSha1File);
                } else {
                    if (!$this->getDriver()->releaseExists($remoteFile)) {
                        $this->log("Package not found in private repository", \Project::MSG_WARN);
                        continue;
                    }

                    $this->getDriver()->downloadRelease($remoteFile, $localFile);
                    $this->getDriver()->downloadRelease($remoteSha1File, $localSha1File);
                }
                $dw_end_time = microtime(true);
                $dw_time_elapsed = round($dw_end_time - $dw_start_time, 3);

                $zipComposerJson = \Composer\Zip::readComposerJsonFromZip("$this->localRepositoryDir/$packageZipName");
                $this->downloadJsonRequirements($zipComposerJson);
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
            $composerLock = new ComposerLock($this->lockFile);

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

        if (\Composer\Version::isDev($version)) {
            $this->getDriver()->downloadBuild("$name-v$version.$format", $this->file);
            if ($this->sha1) {
                $this->getDriver()->downloadBuild("$name-v$version.$format.sha1", $this->file);
            }
        } else {
            $this->getDriver()->downloadRelease("$name-v$version.$format", $this->file);
            if ($this->sha1) {
                $this->getDriver()->downloadRelease("$name-v$version.$format.sha1", $this->file);
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

        $composerJson = new ComposerJson($this->jsonFile);
        $name = $composerJson->getName();
        $version = $composerJson->getVersion();

        if (preg_match('/\.zip$/i', $this->file)) {
            $format = 'zip';
        } elseif (preg_match('/\.tar\.gz/i', $this->file)) {
            $format = 'tar.gz';
        } else {
            throw new \BuildException("File format is not valid ($this->file), allowed values are zip and tar.gz");
        }

        switch ($repo) {
            case 'build':
                $this->getDriver()->uploadBuild("$name-v$version.$format", $this->file);

                if ($this->sha1) {
                    $this->getDriver()->uploadBuild("$name-v$version.$format.sha1", $this->file);
                }
                break;

            case 'release':
                $this->getDriver()->uploadRelease("$name-v$version.$format", $this->file);

                if ($this->sha1) {
                    $this->getDriver()->uploadRelease("$name-v$version.$format.sha1", $this->file);
                }
                break;
        }
    }
}
