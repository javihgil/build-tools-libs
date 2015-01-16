<?php

/*
 * This file is part of the build-tools-lib package.
 *
 * (c) Javi H. Gil <https://github.com/javihgil>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Task;

/**
 * Class AbstractTask
 *
 * @package  Task
 * @author   Javi H. Gil <https://github.com/javihgil>
 */
abstract class AbstractTask extends \Task
{
    /**
     * @param $message
     * @param $command
     * @param array $options
     * @param int $logLevel
     * @param bool $returnResult
     * @return string
     */
    protected function exec(
        $message,
        $command,
        $options = array(),
        $logLevel = \Project::MSG_INFO,
        $returnResult = true
    ) {
        if ($message) {
            $this->log($message, \Project::MSG_INFO);
        }
        $options = implode(' ', $options);
        $execCommand = "$command $options";
        $this->log("  $ $execCommand", $logLevel);

        ob_start();
        $returnedString = system($execCommand, $result);
        ob_end_clean();

        if ($returnResult) {
            echo $returnedString;
            return $result;
        } else {
            return $returnedString;
        }
    }

    /**
     * @param array $sshOptions
     * @return string
     * @throws \BuildException
     */
    protected function sshOptions(array $sshOptions)
    {
        if (empty($sshOptions['host'])) {
            throw new \BuildException('SSH host option is required');
        }

        $sshParameters = "-o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -o LogLevel=quiet";
        if (isset($sshOptions['port'])) {
            $sshParameters .= " -p{$sshOptions['port']}";
        }
        if (isset($sshOptions['privateKey'])) {
            $sshParameters .= " -i {$sshOptions['privateKey']}";
        }

        return $sshParameters;
    }

    /**
     * @param array $sshOptions
     * @return string
     * @throws \BuildException
     */
    protected function sshRemote(array $sshOptions)
    {
        if (empty($sshOptions['host'])) {
            throw new \BuildException('SSH host option is required');
        }

        if (isset($sshOptions['user'])) {
            return " {$sshOptions['user']}@{$sshOptions['host']}";
        } else {
            return ' ' . $sshOptions['host'];
        }
    }

    /**
     * @param $message
     * @param $command
     * @param array $options
     * @param array $sshOptions
     * @param bool $failOnError
     * @param int $logLevel
     * @return mixed
     * @throws \BuildException
     */
    protected function ssh(
        $message,
        $command,
        $options = array(),
        array $sshOptions = array(),
        $failOnError = true,
        $logLevel = \Project::MSG_INFO
    ) {
        if ($message) {
            $this->log($message, \Project::MSG_INFO);
        }
        $options = implode(' ', $options);
        $sshCommand = "ssh " . $this->sshRemote($sshOptions) . ' ' . $this->sshOptions($sshOptions);
        $execCommand = "$sshCommand '$command $options'";
        $this->log("  $ $execCommand", \Project::MSG_DEBUG);
        $this->log("  $ $command $options", $logLevel);
        system($execCommand, $result);

        if ($failOnError && $result) {
            throw new \BuildException("SSH command returns $result");
        }

        if ($result) {
            $this->log("SSH command returns $result, but error was ignored!!", \Project::MSG_WARN);
        }

        return $result;
    }

    /**
     * @param string $paramName
     *
     * @throws \BuildException
     */
    protected function requireParam($paramName)
    {
        if (empty($this->$paramName) && $this->$paramName !== false) {
            throw new \BuildException("$paramName parameter is required.");
        }
    }
}