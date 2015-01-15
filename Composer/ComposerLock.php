<?php

/*
 * This file is part of the build-tools-lib package.
 *
 * (c) Javi H. Gil <https://github.com/javihgil>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer;

/**
 * Class Lock
 *
 * @package Composer
 *
 * @author  Javi H. Gil <https://github.com/javihgil>
 */
class ComposerLock
{

    /**
     * @var array
     */
    protected $lockArray;

    /**
     * @param $lockString
     * @throws \BuildException
     */
    public function __construct($lockString)
    {
        $lock_decoded = json_decode($lockString, true);

        if (!$lock_decoded) {
            throw new \BuildException('Can not decode json string');
        }

        $this->lockArray = $lock_decoded;

        // convert packages into composerJson object
        foreach ($this->lockArray['packages'] as $i => $package) {
            $this->lockArray['packages'][$i] = new ComposerJson(null, json_encode($package));
        }
        foreach ($this->lockArray['packages-dev'] as $i => $package) {
            $this->lockArray['packages-dev'][$i] = new ComposerJson(null, json_encode($package));
        }
    }

    /**
     * Saves data into composer.lock file
     *
     * @param $filePath
     * @throws \BuildException
     */
    public function save($filePath)
    {
        if (!$filePath) {
            throw new \BuildException('No composer.lock file was provided');
        }

        // clone lock data array
        $lockData = $this->lockArray;

        // convert packages into arrays
        $lockData['packages'] = array();
        foreach ($this->lockArray['packages'] as $v) {
            $packageData = $v->getDataArray();

            // remove lock unused fields
            unset($packageData['minimum-stability']);
            unset($packageData['prefer-stable']);

            $lockData['packages'][] = $packageData;
        }
        $lockData['packages-dev'] = array();
        foreach ($this->lockArray['packages-dev'] as $v) {
            $packageData = $v->getDataArray();

            // remove lock unused fields
            unset($packageData['minimum-stability']);
            unset($packageData['prefer-stable']);

            $lockData['packages-dev'][] = $packageData;
        }

        $jsonContent = json_encode($lockData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if (false === file_put_contents($filePath, $jsonContent)) {
            throw new \BuildException(sprintf('Can not save composer lock data into "%s"', $filePath));
        }
    }

    public function hash()
    {
    }

    /**
     * Returns packages
     *
     * @return ComposerJson[]
     */
    public function getPackages()
    {
        return $this->lockArray['packages'];
    }

    /**
     * Returns dev packages
     *
     * @return ComposerJson[]
     */
    public function getDevPackages()
    {
        return $this->lockArray['packages-dev'];
    }

    /**
     * Returns all packages
     *
     * @return ComposerJson[]
     */
    public function getAllPackages()
    {
        return array_merge($this->getPackages(), $this->getDevPackages());
    }

    /**
     * @param ComposerJson $current
     * @param ComposerJson $new
     *
     * @return bool
     */
    public function replacePackage(ComposerJson $current, ComposerJson $new)
    {
        foreach ($this->lockArray['packages'] as $i => $package) {
            if ($package === $current) {
                $this->lockArray['packages'][$i] = $new;
                return true;
            }
        }

        foreach ($this->lockArray['packages-dev'] as $i => $package) {
            if ($package === $current) {
                $this->lockArray['packages-dev'][$i] = $new;
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $filePath
     * @return ComposerLock
     * @throws \BuildException
     */
    public static function createFromFile($filePath)
    {
        if (!file_exists($filePath)) {
            throw new \BuildException(sprintf('Composer json file not found at "%s"', $filePath));
        }

        $composer_json = file_get_contents($filePath);

        return new ComposerLock($composer_json);
    }
}