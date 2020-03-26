<?php


namespace Laijim\Playbook\Entity;

use Laijim\Playbook\Contract\FileWriter;
use Laijim\Playbook\Exception\RunException;
use Laijim\Playbook\Contract\HostManager;
use Laijim\Playbook\Exception\IllegalOperationException;

/**
 * Class LocalHostsFileWriter
 * @package Laijim\Playbook\Entity
 */
class LocalHostsFileWriter extends FileWriter implements HostManager
{


    /**
     * @param array $data
     * @return mixed|void
     */
    public function write(array $data = [])
    {
        if (empty($data)) {
            throw new IllegalOperationException("Hosts list is empty!");
        }

        $fullFilename = sprintf("%s%s%s", $this->path, DIRECTORY_SEPARATOR, $this->hostsFilename);

        $hostsContent = '';
        foreach ($data as $group => $hosts) {//@todo support host variables
            $group = sprintf('[%s]', $group);
            $hosts = implode("\n", $hosts) . "\n\n";
            $hostsContent .= sprintf("%s%s%s", $group, "\n", $hosts);
        }

        try {
            $this->writeFile($this->hostsFilename, $hostsContent);
        } catch (\Exception $e) {
            throw new RunException(sprintf("write ini file %s failed : %s", $fullFilename, $e->getMessage()));
        }

    }
}