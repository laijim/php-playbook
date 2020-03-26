<?php

namespace Laijim\Playbook;

/**
 * Class Task
 * @package Laijim\Playbook
 */
class Task
{
    /**
     * @var string
     */
    public $hosts = 'all';
    /**
     * @var string
     */
    public $variables = "";
    /**
     * @var array
     */
    public $directives = [];

    /**
     * @param $key
     * @param $value
     * @return Task
     */
    public function directive($key, $value)
    {
        $this->directives[$key] = $value;

        return $this;
    }

    /**
     * @param array $data
     * @return Task
     */
    public function addTasks(array $data)
    {
        return $this->directive('task', $data);

    }

    /**
     * @param $hosts
     * @return Task
     */
    public function useHost($hosts)
    {
        $this->hosts = $hosts;

        return $this;
    }

    /**
     * @param array $variables
     * @return Task
     */
    public function useVariables(array $variables)
    {
        $this->variables = $variables;

        return $this;
    }

    public function toArray()
    {
        $vars = get_object_vars($this);
        $array = array();
        foreach ($vars as $key => $value) {
            $array [ltrim($key, '_')] = $value;
        }
        return $array;
    }

}