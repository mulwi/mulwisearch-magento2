<?php

namespace Mulwi\Search\Api\Repository;

use Mulwi\Search\Api\Data\IndexInterface;

interface IndexRepositoryInterface
{
    /**
     * @return IndexInterface[]
     */
    public function getIndexes();

    /**
     * @param string $identifier
     * @return IndexInterface
     * @throws \Exception
     */
    public function getIndex($identifier);
}