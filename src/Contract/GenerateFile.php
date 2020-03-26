<?php

namespace Laijim\Playbook\Contract;

/**
 * Class GenerateFile
 * @package Laijim\Playbook\Contract
 */
abstract class GenerateFile implements Writable
{

    /**
     * @return mixed
     */
    abstract public function init();

    /**
     *
     */
    public function write()
    {
        // TODO: Implement write() method.
    }


}