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
 * Class ForeachModuleTask
 *
 * @author  Javi H. Gil <https://github.com/javihgil>
 */
class ModuleIteratorTask extends AbstractTask implements ActionTaskInterface
{

    /**
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $modulesrefid;

    /**
     * @var string
     */
    protected $task;

    /**
     * @var array
     */
    protected static $moduleStatuses = array();

    const STATUS_SUCCESS = 0;
    const STATUS_FAILED = 1;

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
     * @param string $modulesrefid
     */
    public function setModulesrefid($modulesrefid)
    {
        $this->modulesrefid = $modulesrefid;
    }

    /**
     * @return string
     */
    public function getModulesrefid()
    {
        return $this->modulesrefid;
    }

    /**
     * @param string $task
     */
    public function setTask($task)
    {
        $this->task = $task;
    }

    /**
     * @return string
     */
    public function getTask()
    {
        return $this->task;
    }

    public function __construct()
    {
        $this->commandline = new Commandline();
    }

    /**
     * Creates a nested <arg> tag.
     *
     * @return CommandlineArgument Argument object
     */
    public function createArg()
    {
        return $this->commandline->createArgument();
    }

    /**
     * @throws BuildException
     */
    public function main()
    {
        switch ($this->action) {
            case 'phing':
                $this->phing();
                break;

            case 'summary':
                $this->summary();
                break;

            case 'failatend':
                $this->failAtEnd();
                break;

            default:
                throw new \BuildException("$this->action action is not valid");
        }
    }

    /**
     * @throws BuildException
     */
    public function phing()
    {
        $this->requireParam('modulesrefid');
        $this->requireParam('task');

        $arguments = $this->commandline->getArguments();

        $modules = $this->project->getReference($this->modulesrefid);

        /** @var Module $module */
        foreach ($modules->getModules() as $module) {
            $startTime = microtime(true);
            $this->log('==========================================================================', Project::MSG_INFO);
            $this->log(' ' . $module->getName(), \Project::MSG_INFO);
            $this->log('==========================================================================', Project::MSG_INFO);
            $dir = $module->getName();
            $arguments[] = '-DmoduleName=' . $module->getName();
            $returnValue = $this->exec("Call module task", "cd $dir ; phing $this->task", $arguments);
            $this->log(
                sprintf(' %s call %s task ends with result: %s', $module->getName(), $this->task, $returnValue),
                \Project::MSG_INFO
            );
            $this->log('==========================================================================', Project::MSG_INFO);
            $this->log('', Project::MSG_INFO);
            $endTime = microtime(true);

            self::$moduleStatuses[] = array(
                'name' => $module->getName(),
                'time' => $endTime - $startTime,
                'status' => (bool)$returnValue ? self::STATUS_FAILED : self::STATUS_SUCCESS,
            );
        }
    }


    public function summary()
    {
        $this->log('··········································································', Project::MSG_INFO);
        $this->log(' BUILD SUMMARY', Project::MSG_INFO);
        $this->log('··········································································', Project::MSG_INFO);

        foreach (self::$moduleStatuses as $status) {
            $time = $status['time'];

            if ($time > 60) {
                $formatedTime = round($time / 60) . 'min';
            } else {
                $formatedTime = round($time, 3) . 'sg';
            }

            $message = sprintf(
                '[%s] status %s in %s',
                $status['name'],
                $status['status'] ? 'FAILED' : 'SUCCESS',
                $formatedTime
            );

            if ($status['status']) {
                $this->log($message, Project::MSG_ERR);
            } else {
                $this->log($message, Project::MSG_INFO);
            }
        }
    }

    /**
     * @throws BuildException
     */
    public function failAtEnd()
    {
        foreach (self::$moduleStatuses as $status) {
            if ($status['status']) {
                $this->log("Some of modules status is failed, so the build FAIL", \Project::MSG_ERR);
                throw new \BuildException("Build is failed");
            }
        }
    }
}
