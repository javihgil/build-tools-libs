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

trait UnlessTask
{
    /**
     * @var string
     */
    protected $unless;

    /**
     * @param string $unless
     */
    public function setUnless($unless)
    {
        $this->unless = $unless;
    }

    /**
     * @return boolean
     */
    protected function testUnless()
    {
        if (empty($this->unless)) {
            return true;
        }

        return !(bool)$this->project->getProperty($this->unless);
    }
}
