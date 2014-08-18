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

    public function loadArray($data) {
        return Base::load($data);
    }

    public function loadFile($data) {
        return parent::load($data);
    }

    public function loadTableNode($entity, TableNode $data) {
        $hash = [];
        $key = null;

        foreach ($data->getRow(0) as $col => $header) {
            if ($col === 0 || $header[0] === '@') {
                $key = $header;
            }
        }

        // Parse any inline YAML inside a cell
        foreach ($data->getHash() as $row) {

            foreach ($row as $j => $cell) {
                if ($j[0] === '@') continue;

                $hash[$key][$j] = Inline::parse($cell, false, true);
            }
        }

        return Base::load([$entity => $hash]);
    }
}
