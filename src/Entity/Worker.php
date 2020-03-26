<?php

namespace Laijim\Playbook\Entity;

use Laijim\Playbook\Contract\HostManager;
use Laijim\Playbook\Contract\TaskManager;
use Laijim\Playbook\Contract\VariableManager;
use Laijim\Playbook\Exception\IllegalOperationException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @property $hosts
 * @property $variables
 * @property $tasks
 *
 * Class Worker
 * @package Laijim\Playbook\Entity
 */
class Worker
{
    /**
     * @var
     */
    protected $hosts;

    /**
     * @var
     */
    protected $variables;
    /**
     * @var
     */
    protected $variablesFilename;


    /**
     * @var
     */
    protected $tasks;
    /**
     * @var
     */
    protected $path;

    /**
     * @var array
     */
    protected $yaml = [];

    /**
     * @var HostManager | LocalHostsFileWriter
     */
    private $hostManager;

    /**
     * @var TaskManager | LocalTasksFileWriter
     */
    private $taskManager;

    /**
     * @var VariableManager | LocalVariablesFileWriter
     */
    private $variableManager;

    /**
     * @var int
     */
    private $mode = 0755;
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Worker constructor.
     * @param $path
     * @param HostManager $hostManager
     * @param TaskManager $taskManager
     * @param VariableManager $variableManager
     * @param Filesystem $filesystem
     */
    public function __construct(
        $path,
        HostManager $hostManager,
        TaskManager $taskManager,
        VariableManager $variableManager,
        Filesystem $filesystem
    )
    {
        $this->path = $path;
        $this->filesystem = $filesystem;

        $this->hostManager = $hostManager;
        $this->taskManager = $taskManager;
        $this->variableManager = $variableManager;
    }

    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        if (property_exists($this, $key)) {
            $this->{$key} = $value;
        } else {
            throw new IllegalOperationException(sprintf('key:%s not exists!', $key));
        }
    }

    /**
     * @param array $tasks
     */
    public function generate(array $tasks)
    {
        $this->path = rtrim($this->path, '\\/');
        $this->checkPath($this->path);

        $this->hostManager->setPath($this->path)
            ->write($this->hosts);

        $this->variableManager->setPath($this->path)
            ->write($this->variables);

        $this->taskManager->setPath($this->path)
            ->setVariablesFileMap($this->variableManager->variablesFileMap)
            ->write($tasks);

    }

    /**
     * @param $path
     */
    private function checkPath($path)
    {
        if (!$path) {
            throw new IllegalOperationException("Path is illegal value!");
        }

        $this->filesystem = new Filesystem();

        $varsPath = sprintf("%s%s%s", $path, DIRECTORY_SEPARATOR, $this->variableManager->childPath);
        if (!$this->filesystem->exists($path)) {
            $this->filesystem->mkdir($path, $this->mode);
            $this->filesystem->mkdir($varsPath, $this->mode);
        }
    }

    /**
     * @param $directive
     * @param $config
     */
    public function assign($directive, $config)
    {
        $this->yaml[$directive] = $config;
    }

}
