<?php


namespace Laijim\Playbook\Contract;

use Carbon\Carbon;
use League\Flysystem\Adapter\Local;
use Symfony\Component\Yaml\Yaml;

/**
 * Class FileWriter
 * @package Laijim\Playbook\Contract
 */
abstract class FileWriter
{

    /**
     * @var \League\Flysystem\Filesystem
     */
    protected $filesystem;

    /**
     * main path
     *
     * @var
     */
    protected $path;


    /**
     * @var string
     */
    protected $hostsFilename = 'hosts';

    /**
     * @var string
     */
    protected $variablesPath = "vars";

    /**
     * @var string
     */
    protected $variablesFilename;
    /**
     * @var
     */
    public $variablesFullFilename;
    /**
     * @var array
     */
    public $variablesFileMap = [];

    /**
     * @var string
     */
    protected $playbookFilename = "playbook.yaml";

    /**
     * @param array $data
     * @return mixed
     */
    abstract function write(array $data = []);


    /**
     * @param $path
     * @return $this
     */
    public function setPath($path)
    {
        if ($this->path === $path) {
            return $this;
        }

        $this->path = $path;

        $adapter = new Local($this->path);
        $this->filesystem = new \League\Flysystem\Filesystem($adapter);

        return $this;
    }

    /**
     * @param $filename
     * @param $content
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function writeFile($filename, $content)
    {
        if ($this->filesystem->has($filename)) {
            $backupName = sprintf('%s_bak_%s', $filename, Carbon::now()->format('YmdHis'));
            $this->filesystem->rename($filename, $backupName);
        }

        $this->filesystem->write($filename, $content);
    }

    /**
     * @param $data
     * @return string
     */
    protected function dumpYaml($data){
        return Yaml::dump($data, 8, 4, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
    }

}