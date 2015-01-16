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

/**
 * Class CallTask
 *
 * This class overloads original PhingCallTask for required parameter support
 *
 * @author   Javi H. Gil <https://github.com/javihgil>
 */
class CallTask extends \PhingCallTask
{

    /**
     * @var bool
     */
    protected $required = false;

    /**
     * The called Phing task.
     *
     * @var PhingTask
     */
    protected $callee;

    /**
     * The target to call.
     *
     * @var string
     */
    protected $subTarget;

    /**
     * Whether to inherit all properties from current project.
     *
     * @var boolean
     */
    protected $inheritAll = true;

    /**
     * Whether to inherit refs from current project.
     *
     * @var boolean
     */
    protected $inheritRefs = false;

    /**
     *  If true, pass all properties to the new Phing project.
     *  Defaults to true. Future use.
     *
     * @param boolean new value
     */
    public function setInheritAll($inherit)
    {
        $this->inheritAll = (boolean)$inherit;
    }

    /**
     *  If true, pass all references to the new Phing project.
     *  Defaults to false. Future use.
     *
     * @param boolean new value
     */
    public function setInheritRefs($inheritRefs)
    {
        $this->inheritRefs = (boolean)$inheritRefs;
    }

    /**
     * Alias for createProperty
     *
     * @see createProperty()
     */
    public function createParam()
    {
        if ($this->callee === null) {
            $this->init();
        }
        return $this->callee->createProperty();
    }

    /**
     * Property to pass to the invoked target.
     */
    public function createProperty()
    {
        if ($this->callee === null) {
            $this->init();
        }
        return $this->callee->createProperty();
    }

    /**
     * Target to execute, required.
     */
    public function setTarget($target)
    {
        $this->subTarget = (string)$target;
    }

    /**
     * @param boolean $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     *  init this task by creating new instance of the phing task and
     *  configuring it's by calling its own init method.
     */
    public function init()
    {
        $this->callee = $this->project->createTask("phing");
        $this->callee->setOwningTarget($this->getOwningTarget());
        $this->callee->setTaskName($this->getTaskName());
        $this->callee->setHaltOnFailure(true);
        $this->callee->setLocation($this->getLocation());
        $this->callee->init();
    }

    /**
     * @return array
     */
    protected function getTargetNames()
    {
        $targets = $this->project->getTargets();

        $targetNames = array();
        foreach ($targets as $target) {
            $targetNames[] = $target->getName();
        }

        return $targetNames;
    }

    /**
     * @return bool
     */
    protected function targetIsDefined()
    {
        $targets = $this->getTargetNames();
        return in_array($this->subTarget, $targets);
    }

    /**
     *  hand off the work to the phing task of ours, after setting it up
     *
     * @throws BuildException on validation failure or if the target didn't
     *  execute
     */
    public function main()
    {
        if (!$this->targetIsDefined()) {
            if ($this->required) {
                throw new \BuildException("$this->subTarget target is required and it's not defined");
            } else {
                return;
            }
        }

        $this->log("Running PhingCallTask for target '".$this->subTarget."'", Project::MSG_DEBUG);
        if ($this->callee === null) {
            $this->init();
        }

        if ($this->subTarget === null) {
            throw new BuildException("Attribute target is required.", $this->getLocation());
        }

        $this->callee->setPhingfile($this->project->getProperty("phing.file"));
        $this->callee->setTarget($this->subTarget);
        $this->callee->setInheritAll($this->inheritAll);
        $this->callee->setInheritRefs($this->inheritRefs);
        $this->callee->main();
    }
}