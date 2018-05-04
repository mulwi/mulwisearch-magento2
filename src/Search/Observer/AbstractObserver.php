<?php

namespace Mulwi\Search\Observer;

use Mulwi\Search\Repository\IndexRepository;

abstract class AbstractObserver
{
    /**
     * @var IndexRepository
     */
    protected $indexRepository;

    public function __construct(
        IndexRepository $indexRepository
    ) {
        $this->indexRepository = $indexRepository;
    }
}
