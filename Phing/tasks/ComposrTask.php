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
use Composer\Version;

/**
 * Class ComposrTask
 *
 * @author Javi H. Gil <https://github.com/javihgil>
 */
class ComposrTask extends AbstractTask implements ActionTaskInterface
{

    /**
     * @var string
     */
    protected $pharFile = 'composer.phar';

    /**
     * @var string
     */
    protected $lockFile = 'composer.lock';

    /**
     * @var string
     */
    protected $jsonFile = 'composer.json';

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
    protected $property;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var string
     */
    protected $dir = '.';

    /**
     * @var boolean
     */
    protected $dev = false;

    /**
     * @var boolean
     */
    protected $optimizeautoloader;

    /**
     * @var bool
     */
    protected $increment = false;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var string
     */
    protected $forceVersionType;

    /**
     * @var string
     */
    protected $dependencyPackage;

    /**
     * @var string
     */
    protected $dependencyGroup;

    /**
     * @var string
     */
    protected $dependencyPattern;

    /**
     * @var bool
     */
    protected $preferDist = true;

    /**
     * @var bool
     */
    protected $noProgress = true;

    /**
     * @var bool
     */
    protected $noInteraction = true;

    /**
     * @var bool
     */
    protected $profile = true;

    /**
     * @var string
     */
    protected $verbosity = 'vv';

    /**
     * @var string
     */
    protected $logFile;

    /**
     * @param string $localRepositoryDir
     */
    public function setLocalRepositoryDir($localRepositoryDir)
    {
        $this->localRepositoryDir = $localRepositoryDir;
    }

    /**
     * @return string
     */
    public function getLocalRepositoryDir()
    {
        return $this->localRepositoryDir;
    }

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
     * @param string $lockFile
     */
    public function setLockFile($lockFile)
    {
        $this->lockFile = $lockFile;
    }

    /**
     * @return string
     */
    public function getLockFile()
    {
        return $this->lockFile;
    }

    /**
     * @param string $pharFile
     */
    public function setPharFile($pharFile)
    {
        $this->pharFile = $pharFile;
    }

    /**
     * @return string
     */
    public function getPharFile()
    {
        return $this->pharFile;
    }

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
     * @param string $property
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

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
     * @param boolean $dev
     */
    public function setDev($dev)
    {
        $this->dev = $dev;
    }

    /**
     * @return boolean
     */
    public function getDev()
    {
        return $this->dev;
    }

    /**
     * @param boolean $increment
     */
    public function setIncrement($increment)
    {
        $this->increment = $increment;
    }

    /**
     * @return boolean
     */
    public function getIncrement()
    {
        return $this->increment;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $forceVersionType
     */
    public function setForceVersionType($forceVersionType)
    {
        $this->forceVersionType = $forceVersionType;
    }

    /**
     * @return string
     */
    public function getForceVersionType()
    {
        return $this->forceVersionType;
    }

    /**
     * @param boolean $optimizeautoloader
     */
    public function setOptimizeautoloader($optimizeautoloader)
    {
        $this->optimizeautoloader = $optimizeautoloader;
    }

    /**
     * @return boolean
     */
    public function getOptimizeautoloader()
    {
        return $this->optimizeautoloader;
    }

    /**
     * @param string $dependencyGroup
     */
    public function setDependencyGroup($dependencyGroup)
    {
        $this->dependencyGroup = $dependencyGroup;
    }

    /**
     * @return string
     */
    public function getDependencyGroup()
    {
        return $this->dependencyGroup;
    }

    /**
     * @param string $dependencyPackage
     */
    public function setDependencyPackage($dependencyPackage)
    {
        $this->dependencyPackage = $dependencyPackage;
    }

    /**
     * @return string
     */
    public function getDependencyPackage()
    {
        return $this->dependencyPackage;
    }

    /**
     * @param string $dependencyPattern
     */
    public function setDependencyPattern($dependencyPattern)
    {
        $this->dependencyPattern = $dependencyPattern;
    }

    /**
     * @return string
     */
    public function getDependencyPattern()
    {
        return $this->dependencyPattern;
    }

    /**
     * @return boolean
     */
    public function isPreferDist()
    {
        return $this->preferDist;
    }

    /**
     * @param boolean $preferDist
     * @return $this
     */
    public function setPreferDist($preferDist)
    {
        $this->preferDist = $preferDist;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isNoProgress()
    {
        return $this->noProgress;
    }

    /**
     * @param boolean $noProgress
     * @return $this
     */
    public function setNoProgress($noProgress)
    {
        $this->noProgress = $noProgress;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isProfile()
    {
        return $this->profile;
    }

    /**
     * @param boolean $profile
     * @return $this
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
        return $this;
    }

    /**
     * @return string
     */
    public function getVerbosity()
    {
        return $this->verbosity;
    }

    /**
     * @param string $verbosity
     * @return $this
     */
    public function setVerbosity($verbosity)
    {
        $this->verbosity = $verbosity;
        return $this;
    }

    /**
     * @param string $logFile
     * @return $this
     */
    public function setLogFile($logFile)
    {
        $this->logFile = $logFile;
        return $this;
    }

    /**
     * @param boolean $noInteraction <param_description>
     *
     * @return $this
     */
    public function setNoInteraction($noInteraction)
    {
        $this->noInteraction = $noInteraction;

        return $this;
    }

    /**
     * @throws BuildException
     */
    public function main()
    {
        switch ($this->action) {
            case 'get':
                $this->getComposer();
                break;

            case 'self-update':
                $this->pharSelfUpdate();
                break;

            case 'install':
                $this->install();
                break;

            case 'check-release-dependencies':
                $this->checkReleaseDependencies();
                break;

            case 'check-lock-updated':
                $this->checkLockUpdated();
                break;

            case 'check-dev-version':
                $this->checkDevVersion();
                break;

            case 'get-version':
                $this->getJsonVersion();
                break;

            case 'version-update':
                $this->versionUpdate();
                break;

            case 'dependency-update':
                $this->dependencyUpdate();
                break;

            case 'export':
                $this->export();
                break;

            case 'log-installed':
                $this->logInstalled();
                break;

            default:
                throw new \BuildException("$this->action action is not valid");
        }
    }

    /**
     * Gets composer.phar or self updates
     * @throws BuildException
     */
    public function getComposer()
    {
        $this->requireParam('pharFile');
        $this->log('Get composer.phar', \Project::MSG_INFO);

        if (file_exists($this->pharFile)) {
            $this->pharSelfUpdate();
            return;
        }

        system("php -r \"eval('?>'.file_get_contents('https://getcomposer.org/installer'));\"");
        $baseDir = $this->project->getBasedir();
        $this->log("composer.phar installed in $baseDir", \Project::MSG_INFO);
    }

    /**
     * Show installed packages
     */
    public function logInstalled()
    {
        $this->requireParam('pharFile');
        $this->requireParam('logFile');
        $this->requireParam('jsonFile');
        $options = array(
            "-d $this->dir",
            "--installed",
        );

        $composerJson = ComposerJson::createFromFile($this->jsonFile);

        $packageName = "{$composerJson->getName()} {$composerJson->getVersion()}";
        $pad = str_pad('', strlen($packageName), '=');

        $result = $this->exec(
            "Composer show",
            "echo '$packageName\n$pad' >> $this->logFile ; php -d memory_limit=-1 $this->pharFile show >> $this->logFile",
            $options,
            \Project::MSG_INFO,
            true,
            false // no buffer
        );
    }

    /**
     * Self updates composer.phar
     * @throws BuildException
     */
    public function pharSelfUpdate()
    {
        $this->requireParam('pharFile');
        $this->exec("Composer self update", "php $this->pharFile self-update --no-progress");
    }

    /**
     * @throws BuildException
     */
    public function checkReleaseDependencies()
    {
        $this->requireParam('jsonFile');
        $composerJson = ComposerJson::createFromFile($this->jsonFile);

        $requirements = $composerJson->getAllRequirements();

        $fail = false;
        foreach ($requirements as $package => $version) {
            if (preg_match('/\-dev$/i', $version)) {
                $this->log("Releases does not allow dev dependencies: $package ($version)", \Project::MSG_ERR);
                $fail = true;
            }
        }

        if ($fail) {
            throw new \BuildException("Dev dependencies found, check your composer.json file.");
        }
    }

    /**
     * @throws BuildException
     */
    public function checkDevVersion()
    {
        $this->requireParam('jsonFile');
        $composerJson = ComposerJson::createFromFile($this->jsonFile);
        $version = $composerJson->getVersion();

        if (!Version::isDev($version)) {
            $this->log("Check dev version: FAILED ($version)", \Project::MSG_ERR);
            throw new \BuildException("Required dev version");
        } else {
            $this->log("Check dev version: SUCCESS ($version)", \Project::MSG_INFO);
        }
    }

    /**
     * @throws BuildException
     */
    public function install()
    {
        $this->requireParam('pharFile');
        $options = array(
            "-d $this->dir",
            $this->dev ? '--dev' : '--no-dev',
            $this->preferDist ? '--prefer-dist' : '',
            $this->noProgress ? '--no-progress' : '',
            $this->noInteraction ? '--no-interaction' : '',
            $this->profile ? '--profile' : '',
            $this->verbosity ? "-$this->verbosity" : '',
        );

        if ($this->optimizeautoloader) {
            $options[] = '--optimize-autoloader';
        }

        $result = $this->exec(
            "Composer install",
            "php -d memory_limit=-1 $this->pharFile install",
            $options,
            \Project::MSG_INFO,
            true,
            false // no buffer
        );

        if ((bool)$result) {
            $this->log("Composer command returns $result", \Project::MSG_ERR);
            throw new \BuildException("Composer install failed");
        }
    }

    /**
     * @throws BuildException
     */
    public function getJsonVersion()
    {
        $this->requireParam('jsonFile');
        $this->requireParam('property');

        $composerJson = ComposerJson::createFromFile($this->jsonFile);

        if ($this->forceVersionType == 'release') {
            $version = str_ireplace('-dev', '', $composerJson->getVersion());
        } else {
            $version = $composerJson->getVersion();
        }

        if ($this->increment) {
            $versionArray = explode('.', str_ireplace('-dev', '', $version));
            $lastNumber = sizeof($versionArray) - 1;
            $versionArray[$lastNumber] = (int)$versionArray[$lastNumber] + 1;
            $version = implode('.', $versionArray) . '-dev';
        }

        $this->project->setNewProperty($this->getProperty(), $version);
    }

    /**
     * @throws BuildException
     */
    public function versionUpdate()
    {
        $this->requireParam('jsonFile');
        $this->requireParam('version');

        $version = $this->getVersion();

        if ($this->forceVersionType == 'dev') {
            $version = Version::dev($version);
        } elseif ($this->forceVersionType == 'release') {
            $version = Version::release($version);
        }

        $composerJson = ComposerJson::createFromFile($this->jsonFile);
        $composerJson->setVersion($version);
        $composerJson->save($this->jsonFile);

        $projectName = $composerJson->getName();
        $this->log("Version update success to $version for $projectName in $this->jsonFile", Project::MSG_INFO);
    }

    /**
     * @throws BuildException
     */
    public function dependencyUpdate()
    {
        $this->requireParam('jsonFile');
        $this->requireParam('version');

        if (!$this->dependencyGroup && !$this->dependencyPackage && !$this->dependencyPattern) {
            $this->log("dependencyGroup, dependencyPackage or dependencyPattern is required", \Project::MSG_ERR);
            throw new \BuildException("dependency update failed");
        }

        $version = $this->getVersion();
        $composerJson = ComposerJson::createFromFile($this->jsonFile);

        if ($this->dependencyGroup) {
            $dependencies = $composerJson->getFilteredRequirements(
                '/^' . addcslashes(preg_quote($this->dependencyGroup), '/') . '\//i'
            );
        }
        if ($this->dependencyPackage) {
            $dependencies = $composerJson->getFilteredRequirements(
                '/^' . addcslashes(preg_quote($this->dependencyPackage), '/') . '$/i'
            );
        }
        if ($this->dependencyPattern) {
            $dependencies = $composerJson->getFilteredRequirements('/' . $this->dependencyPattern . '/i');
        }

        if (empty($dependencies)) {
            $this->log("No dependencies found for update in " . $composerJson->getName(), Project::MSG_INFO);
            return;
        }
        foreach ($dependencies as $dependency => $currentVersion) {
            $this->log(
                "Update dependency $dependency from $currentVersion to version $version in " . $composerJson->getName(),
                Project::MSG_INFO
            );
            $composerJson->setDependencyVersion($dependency, $version);
        }

        $composerJson->save($this->jsonFile);
    }

    /**
     * Checks if composer.json file has changes from composer.lock, and removes lock
     */
    public function checkLockUpdated()
    {
        $this->requireParam('jsonFile');
        $this->requireParam('lockFile');

        $this->log("Checking composer.lock file...", Project::MSG_INFO);

        if (file_exists($this->getLockFile())) {
            $md5 = md5_file($this->getJsonFile());
            $lock = json_decode(file_get_contents($this->getLockFile()), true);
            $updateRequired = $md5 != $lock['hash'];

            if ($updateRequired) {
                $this->log("Composer.json file's md5 is different than lock's hash", Project::MSG_WARN);
                $this->exec("Remove composer.lock file (install is quicker than update)", "rm -f $this->lockFile");
            } else {
                $this->log("Composer.lock file is up to date", Project::MSG_INFO);
            }
        }
    }

    /**
     * Exports given property to the project
     */
    public function export()
    {
        $this->requireParam('jsonFile');
        $this->requireParam('value');
        $this->requireParam('property');

        $composerJson = ComposerJson::createFromFile($this->jsonFile);

        switch ($this->value) {
            case 'name':
                $this->getProject()->setNewProperty($this->getProperty(), $composerJson->getName());
                break;

            case 'version':
                $this->getProject()->setNewProperty($this->getProperty(), $composerJson->getVersion());
                break;

            case 'type':
                $this->getProject()->setNewProperty($this->getProperty(), $composerJson->getType());
                break;

            default:
                $this->log("Export $this->value json property is not implemented.", \Project::MSG_WARN);
        }
    }
}
