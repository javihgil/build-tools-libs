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
 * Class SymfonyTask
 *
 * @author Javi H. Gil <https://github.com/javihgil>
 */
class SymfonyTask extends AbstractTask implements ActionTaskInterface
{

    /**
     * @var string
     */
    protected $console = 'app/console';

    /**
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $env = 'dev';

    /**
     * @var bool
     */
    protected $warmup = false;

    /**
     * @var bool
     */
    protected $symlink;

    /**
     * @var bool
     */
    protected $relative = false;

    /**
     * @var bool
     */
    protected $optionalWarmers = true;

    /**
     * @var string
     */
    protected $dir = '.';

    /**
     * @var string
     */
    protected $path;

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
     * @param string $console
     */
    public function setConsole($console)
    {
        $this->console = $console;
    }

    /**
     * @return string
     */
    public function getConsole()
    {
        return $this->console;
    }

    /**
     * @param string $env
     */
    public function setEnv($env)
    {
        $this->env = $env;
    }

    /**
     * @return string
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @param boolean $warmup
     */
    public function setWarmup($warmup)
    {
        $this->warmup = $warmup;
    }

    /**
     * @return boolean
     */
    public function getWarmup()
    {
        return $this->warmup;
    }

    /**
     * @param boolean $symlink
     */
    public function setSymlink($symlink)
    {
        $this->symlink = $symlink;
    }

    /**
     * @return boolean
     */
    public function getSymlink()
    {
        return $this->symlink;
    }

    /**
     * @param boolean $relative
     */
    public function setRelative($relative)
    {
        $this->relative = $relative;
    }

    /**
     * @return boolean
     */
    public function getRelative()
    {
        return $this->relative;
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
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @param boolean $optionalWarmers
     */
    public function setOptionalWarmers($optionalWarmers)
    {
        $this->optionalWarmers = $optionalWarmers;
    }

    /**
     * @return boolean
     */
    public function getOptionalWarmers()
    {
        return $this->optionalWarmers;
    }

    /**
     * @throws BuildException
     */
    public function main()
    {
        switch ($this->action) {
            case 'remove-cache':
                $this->removeCache();
                break;

            case 'remove-logs':
                $this->removeLogs();
                break;

            case 'cache-clear':
                $this->cacheClear();
                break;

            case 'cache-warm':
                $this->cacheWarm();
                break;

            case 'remove-assets':
                $this->removeAssets();
                break;

            case 'assets-install':
                $this->assetsInstall();
                break;

            case 'assetic-dump':
                $this->asseticDump();
                break;

            case 'doctrine-schema-create':
                $this->doctrineSchemaCreate();
                break;

            case 'doctrine-schema-update':
                $this->doctrineSchemaUpdate();
                break;

            default:
                throw new \BuildException("$this->action action is not valid");
        }
    }

    /**
     * @throws BuildException
     */
    public function removeCache()
    {
        $this->requireParam('dir');
        $this->exec("Remove symfony cache", "cd $this->dir ; rm app/cache/* -Rf");
    }

    /**
     * @throws BuildException
     */
    public function removeLogs()
    {
        $this->requireParam('dir');
        $this->exec("Remove symfony logs", "cd $this->dir ; rm app/logs/* -Rf");
    }

    /**
     * @throws BuildException
     */
    public function removeAssets()
    {
        $this->requireParam('dir');
        $this->exec("Remove symfony assets", "cd $this->dir ; rm web/bundles/* -Rf");
    }

    /**
     * @throws BuildException
     */
    public function cacheClear()
    {
        $this->requireParam('env');
        $this->requireParam('warmup');
        $this->requireParam('console');
        $this->requireParam('dir');

        $options = array(
            "--env=$this->env",
            $this->warmup ? '' : '--no-warmup',
        );

        $this->exec('Symfony cache clear', "cd $this->dir ; php $this->console cache:clear", $options);
    }

    /**
     * @throws BuildException
     */
    public function cacheWarm()
    {
        $this->requireParam('env');
        $this->requireParam('console');
        $this->requireParam('optionalWarmers');
        $this->requireParam('dir');

        $options = array(
            $this->optionalWarmers ? '' : '--no-optional-warmers',
            "--env=$this->env",
        );

        $this->exec('Symfony cache clear', "cd $this->dir ; php $this->console cache:warm", $options);
    }

    /**
     * @throws BuildException
     */
    public function assetsInstall()
    {
        $this->requireParam('env');
        $this->requireParam('console');
        $this->requireParam('symlink');
        $this->requireParam('relative');
        $this->requireParam('dir');
        $this->requireParam('path');

        $options = array(
            $this->symlink ? '--symlink' : '',
            $this->relative ? '--relative' : '',
            $this->path,
            "--env=$this->env",
        );

        $this->exec('Symfony assets install', "cd $this->dir ; php $this->console assets:install", $options);
    }

    /**
     * @throws BuildException
     */
    public function asseticDump()
    {
        $this->requireParam('env');
        $this->requireParam('console');
        $this->requireParam('dir');

        $options = array(
            "--env=$this->env",
        );

        $this->exec('Symfony assetic dump', "cd $this->dir ; php $this->console assetic:dump", $options);
    }

    /**
     * @throws BuildException
     */
    public function doctrineSchemaCreate()
    {
        $this->requireParam('console');
        $this->requireParam('env');
        $this->requireParam('dir');

        $options = array(
            "--env=$this->env",
        );

        $this->exec(
            'Symfony doctrine schema create',
            "cd $this->dir ; php $this->console doctrine:schema:create",
            $options
        );
    }

    /**
     * @throws BuildException
     */
    public function doctrineSchemaUpdate()
    {
        $this->requireParam('console');
        $this->requireParam('env');
        $this->requireParam('dir');

        $options = array(
            "--env=$this->env",
        );

        $this->exec(
            'Symfony doctrine schema update query dump',
            "cd $this->dir ; php $this->console doctrine:schema:update --dump-sql",
            $options
        );
        $this->exec(
            'Symfony doctrine schema update query exec',
            "php $this->console doctrine:schema:update --force",
            $options
        );
    }
}
