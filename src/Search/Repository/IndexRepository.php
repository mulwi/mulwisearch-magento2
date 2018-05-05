<?php

namespace Mulwi\Search\Repository;

use Magento\Framework\ObjectManagerInterface;
use Mulwi\Search\Api\Data\IndexInterface;
use Mulwi\Search\Api\Repository\IndexRepositoryInterface;
use Mulwi\Search\Index\AbstractIndex;

class IndexRepository implements IndexRepositoryInterface
{
    /**
     * @var AbstractIndex[]
     */
    private $indexes;

    public function __construct(
        array $indexes = []
    )
    {
        $this->indexes = $indexes;
    }

    /**
     * @return AbstractIndex[]
     */
    public function getIndexes()
    {
        return $this->indexes;
    }


    /**
     * {@inheritdoc}
     */
    public function getIndex($identifier)
    {
        foreach ($this->getIndexes() as $index) {
            if ($index->getIdentifier() === $identifier) {
                return $index;
            }
        }

        throw new \Exception("Undefined index");
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