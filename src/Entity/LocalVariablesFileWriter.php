<?php

namespace Laijim\Playbook\Entity;

use function Couchbase\defaultDecoder;
use Laijim\Playbook\Contract\FileWriter;
use Laijim\Playbook\Contract\VariableManager;
use Laijim\Playbook\Exception\IllegalOperationException;
use Laijim\Playbook\Exception\RunException;
use Ramsey\Uuid\Uuid;

/**
 * Class LocalVariablesFileWriter
 * @package Laijim\Playbook\Entity
 */
class LocalVariablesFileWriter extends FileWriter implements VariableManager
{

    /**
     * @var string
     */
    public $childPath = "vars";

    /**
     * @param null $filename
     */
    public function setFilename($filename = null)
    {
        $this->variablesFilename = $filename ?: Uuid::uuid4()->toString();
    }

    /**
     * @param array $data
     * @return mixed|void
     */
    public function write(array $data = [])
    {
        if (empty($data)) {
            throw new IllegalOperationException("variables is empty!");
        }

        $filename = '';
        try {
            foreach ($data as $name => $values) {
                $yaml = $this->dumpYaml($values);
                $filename = sprintf("%s%s%s.yaml",
                    $this->childPath,
                    DIRECTORY_SEPARATOR,
                    $name);

                $this->writeFile($filename, $yaml);

                $this->variablesFileMap[$name] = sprintf("./%s", $filename);
            }

        } catch (\Exception $e) {
            throw new RunException(sprintf("write variables file %s failed : %s", $filename, $e->getMessage()));
        }

    }
}