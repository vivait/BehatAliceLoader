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
        // Parse any inline YAML inside a cell
        foreach ($data->getHash() as $row) {
	        $key = current( $row );

            foreach ($row as $j => $cell) {
                $hash[$key][$j] = Inline::parse($cell);
            }
        }

        return Base::load([$entity => $hash]);
    }
}
