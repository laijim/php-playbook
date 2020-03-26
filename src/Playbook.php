<?php

namespace Laijim\Playbook;

use Laijim\Playbook\Entity\Worker;

/**
 * Class Playbook
 * @package Laijim\Playbook
 */
class Playbook
{

    /**
     * @var Worker | null
     */
    public $worker = null;

    /**
     * @var array
     */
    private $tasks = [];

    /**
     * @var array
     */
    public $variableFilesList = [];

    /**
     * Playbook constructor.
     * @param Worker $work
     */
    public function __construct(Worker $work)
    {
        $this->worker = $work;
    }

    /**
     * @param array $hosts
     * @return $this
     */
    public function setHosts(array $hosts)
    {
        $this->worker->hosts = $hosts;

        return $this;
    }

    /**
     * @param array $variables
     * @param string $filename
     * @return $this
     */
    public function setVariables(array $variables, $filename = "")
    {
        $this->worker->variables = $variables;
        $this->worker->variablesFilename = $filename;

        return $this;
    }

    /**
     * @param array $tasks
     * @return $this
     */
    public function setTasks(array $tasks)
    {
        $this->worker->tasks = $tasks;

        return $this;
    }

    /**
     * @return $this
     */
    public function generate()
    {
        $this->worker->generate($this->tasks);
        return $this;
    }

    /**
     * @param $directive
     * @param $config
     * @return $this
     */
    public function assign($directive, $config)
    {
        $this->worker->assign($directive, $config);
        return $this;
    }

    /**
     * @param Task $task
     * @return $this
     */
    public function register(Task $task)
    {
        $this->tasks[] = $task;

        return $this;
    }

}