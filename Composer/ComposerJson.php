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
 * Class ComposerJson
 *
 * @package Composer
 */
class ComposerJson
{

    /**
     * @var array
     */
    protected $jsonArray;

    /**
     * @param string $jsonString
     *
     * @throws \BuildException
     */
    public function __construct($jsonString)
    {
        $jsonDecoded = json_decode($jsonString, true);

        if (!$jsonDecoded) {
            throw new \BuildException('Can not decode json string');
        }

        $this->jsonArray = $jsonDecoded;
    }

    /**
     * Saves data into composer.json file
     *
     * @param string $filePath
     *
     * @throws \BuildException
     */
    public function save($filePath)
    {
        if (!$filePath) {
            throw new \BuildException('No composer.json file was provided');
        }

        $jsonData = $this->getDataArray();

        $jsonContent = json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if (false === file_put_contents($filePath, $jsonContent)) {
            throw new \BuildException(sprintf('Can not save composer json data into "%s"', $filePath));
        }
    }

    /**
     * @return array
     */
    public function getRequires()
    {
        if (!isset($this->jsonArray['require'])) {
            return array();
        } else {
            return $this->jsonArray['require'];
        }
    }

    /**
     * @param array $requires
     */
    public function setRequires(array $requires)
    {
        $this->jsonArray['require'] = $requires;
    }

    /**
     * @return array
     */
    public function getRequiresDev()
    {
        if (!isset($this->jsonArray['require-dev'])) {
            return array();
        } else {
            return $this->jsonArray['require-dev'];
        }
    }

    /**
     * @param array $requiresDev
     */
    public function setRequiresDev(array $requiresDev)
    {
        $this->jsonArray['require-dev'] = $requiresDev;
    }

    /**
     * @return array
     */
    public function getAllRequirements()
    {
        return array_merge($this->getRequires(), $this->getRequiresDev());
    }

    /**
     * @param string $filterExprReg
     *
     * @return array
     */
    public function getFilteredRequirements($filterExprReg)
    {
        $requirements = $this->getAllRequirements();

        if ($filterExprReg) {
            foreach ($requirements as $package => $version) {
                if (!preg_match($filterExprReg, $package)) {
                    unset($requirements[$package]);
                }
            }
        }

        return $requirements;
    }

    /**
     * @param string $url
     * @param string $type
     *
     * @return bool
     */
    public function addRepository($url, $type = 'artifact')
    {
        if (isset($this->jsonArray['repositories'])) {
            foreach ($this->jsonArray['repositories'] as $repo) {
                if ($repo['url'] == $url) {
                    return false;
                }
            }
        }

        $this->jsonArray['repositories'][] = array(
            'url' => $url,
            'type' => $type,
        );

        return true;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return isset($this->jsonArray['name']) ? $this->jsonArray['name'] : '';
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return isset($this->jsonArray['version']) ? $this->jsonArray['version'] : '';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return isset($this->jsonArray['type']) ? $this->jsonArray['type'] : '';
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->jsonArray['version'] = $version;
    }

    /**
     * @return string
     */
    public function getDistType()
    {
        return isset($this->jsonArray['dist']['type']) ? $this->jsonArray['dist']['type'] : '';
    }

    /**
     * @return string
     */
    public function getDistUrl()
    {
        return isset($this->jsonArray['dist']['url']) ? $this->jsonArray['dist']['url'] : '';
    }

    /**
     * @return string
     */
    public function getDistShasum()
    {
        return isset($this->jsonArray['dist']['shasum']) ? $this->jsonArray['dist']['url'] : '';
    }

    /**
     * @param string $shasum
     */
    public function setDistShasum($shasum)
    {
        $this->jsonArray['dist']['shasum'] = $shasum;
    }

    /**
     * @return array
     */
    public function getDistArray()
    {
        return $this->jsonArray['dist'];
    }

    /**
     * @param array $dist
     */
    public function setDistArray(array $dist)
    {
        $this->jsonArray['dist'] = $dist;
    }

    /**
     * @param string $key
     */
    public function unsetKey($key)
    {
        if (isset($this->jsonArray[$key])) {
            unset($this->jsonArray[$key]);
        }
    }

    /**
     * @param string $dependency
     * @param string $version
     */
    public function setDependencyVersion($dependency, $version)
    {
        if (isset($this->jsonArray['require-dev'][$dependency])) {
            $this->jsonArray['require-dev'][$dependency] = $version;
        } else {
            $this->jsonArray['require'][$dependency] = $version;
        }
    }

    /**
     * @return array
     */
    public function getDataArray()
    {
        $dataArray = $this->jsonArray;

        if (empty($dataArray['require'])) {
            unset($dataArray['require']);
        }

        if (empty($dataArray['require-dev'])) {
            unset($dataArray['require-dev']);
        }

        return $dataArray;
    }

    /**
     * @param string $zipPath
     *
     * @return ComposerJson
     * @throws \BuildException
     */
    public static function createFromZip($zipPath)
    {
        $composerJson = file_get_contents("zip://$zipPath#composer.json");

        if (false === $composerJson) {
            throw new \BuildException(sprintf('Composer json file not found inside zip file "%s"', $zipPath));
        }

        return new ComposerJson($composerJson);
    }

    /**
     * @param string $filePath
     *
     * @return ComposerJson
     * @throws \BuildException
     */
    public static function createFromFile($filePath)
    {
        if (!file_exists($filePath)) {
            throw new \BuildException(sprintf('Composer json file not found at "%s"', $filePath));
        }

        $composerJson = file_get_contents($filePath);

        return new ComposerJson($composerJson);
    }
}
