<?php

/*
 * This file is part of the deploy package.
 *
 * (c) Javi H. Gil <https://github.com/javihgil>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Task\Traits;

trait IfTask
{
    /**
     * @var string
     */
    protected $if;

    /**
     * @param string $if
     */
    public function setIf($if)
    {
        $this->if = $if;
    }

    /**
     * @return boolean
     */
    protected function testIf()
    {
        if (empty($this->if)) {
            return true;
        }

        return (bool)$this->project->getProperty($this->if);
    }
}
