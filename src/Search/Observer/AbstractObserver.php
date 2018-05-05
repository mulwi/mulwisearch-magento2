<?php

namespace Mulwi\Search\Observer;

use Mulwi\Search\Service\QueueService;

abstract class AbstractObserver
{
    /**
     * @var QueueService
     */
    protected $queueService;

    public function __construct(
        QueueService $queueService
    )
    {
        $this->queueService = $queueService;
    }
}
