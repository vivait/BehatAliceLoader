<?php
namespace Vivait\BehatAliceLoader;

use Behat\Gherkin\Node\TableNode;
use Nelmio\Alice\Loader\Base;
use Nelmio\Alice\Loader\Yaml;
use Symfony\Component\Yaml\Inline;

class BehatAliceLoader extends Yaml
{
    /**
     * {@inheritDoc}
     */
    public function load($data)
    {
        if (is_array($data)) {
            $value = reset($data);
            if (count($data) === 1 && $value instanceOf TableNode) {
                return $this->loadTableNode(key($data), $value);
            }

            return $this->loadArray($data);
        }

        return $this->loadFile($data);
    }

    /**
     * @param array $data An array of fixtures
     * @return array An array of entities
     */
    public function loadArray($data)
    {
        return Base::load($data);
    }

    /**
     * @param string $data The filename to load
     * @return array An array of entities
     */
    public function loadFile($data)
    {
        return parent::load($data);
    }

    /**
     * @param string    $entity The entity class name
     * @param TableNode $data A TableNode containing the data
     * @return array An array of entities
     */
    public function loadTableNode($entity, TableNode $data)
    {
        $hash = [];
        $reference_col = $this->getReferenceColumn($data);

        // Parse any inline YAML inside a cell
        foreach ($data->getHash() as $row) {
            $key = $row[$reference_col];

            foreach ($row as $j => $cell) {
                if ($j[0] === '@') {
                    continue;
                }

                $hash[$key][$j] = Inline::parse($cell, false, true);
            }
        }

        return $this->loadArray([$entity => $hash]);
    }

    /**
     * @param TableNode $data
     * @return null
     */
    private function getReferenceColumn(TableNode $data)
    {
        $default_col = null;

        foreach ($data->getRow(0) as $col => $header) {
            if ($header[0] === '@') {
                return $header;
            }
            else if ($col === 0) {
                $default_col = $header;
            }
        }

        return $default_col;
    }
}
