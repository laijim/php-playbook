<?php

namespace Laijim\Playbook\Entity;

use Laijim\Playbook\Contract\FileWriter;
use Laijim\Playbook\Contract\TaskManager;
use Laijim\Playbook\Exception\IllegalOperationException;
use Laijim\Playbook\Exception\RunException;

/**
 * Class LocalTasksFileWriter
 * @package Laijim\Playbook\Entity
 */
class LocalTasksFileWriter extends FileWriter implements TaskManager
{

    /**
     * @param array $variableFiles
     * @return $this
     */
    public function setVariablesFileMap(array $variableFiles = [])
    {
        $this->variablesFileMap = $variableFiles;
        return $this;
    }

    /**
     * @param array $data
     * @return mixed|void
     */
    public function write(array $data = [])
    {

        if (empty($data)) {
            throw new IllegalOperationException("task is empty!");
        }
        try {
            $list = [];
            foreach ($data as $task) {
                //@todo  do more check things before
                $content = $task->toArray();
                if (array_key_exists('variables', $content)) {
                    foreach ($content['variables'] as $variable) {
                        $content['vars_files'][] = $this->variablesFileMap[$variable];
                    }
                    unset($content['variables']);

                    $tasks = $content['directives'];
                    $content = array_merge($content, $tasks);
                    unset($content['directives']);
                } else {
                    throw new RunException("variables not exists! %s", json_encode($content));
                }

                $list[] = $content;
            }

            $yaml = $this->dumpYaml($list);
            $this->writeFile($this->playbookFilename, $yaml);
        } catch (\Exception $e) {
            throw new RunException(sprintf("write task file %s failed : %s", $this->playbookFilename, $e->getMessage()));
        }

    }
}