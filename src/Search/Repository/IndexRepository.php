<?php

namespace Mulwi\Search\Repository;

use Magento\Framework\ObjectManagerInterface;
use Mulwi\Search\Index\AbstractIndex;

class IndexRepository
{
    /**
     * @var AbstractIndex[]
     */
    private $indexes;

    public function __construct(
        array $indexes = []
    ) {
        $this->indexes = $indexes;
    }

    /**
     * @return AbstractIndex[]
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    public function updateDocument($object)
    {
        foreach ($this->indexes as $index) {
            foreach ($index->getIndexableClasses() as $class) {
                if ($object instanceof $class) {
                    $index->updateDocument($object);
                }
            }
        }
    }

    public function deleteDocument($object)
    {
        foreach ($this->indexes as $index) {
            foreach ($index->getIndexableClasses() as $class) {
                if ($object instanceof $class) {
                    $index->deleteDocument($object);
                }
            }
        }
    }
}