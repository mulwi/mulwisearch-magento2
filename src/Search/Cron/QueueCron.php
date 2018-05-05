<?php

namespace Mulwi\Search\Cron;

use Mulwi\Search\Api\Data\QueueInterface;
use Mulwi\Search\Api\Repository\QueueRepositoryInterface;
use Mulwi\Search\Service\QueueService;

class QueueCron
{
    /**
     * @var QueueRepositoryInterface
     */
    private $repository;

    /**
     * @var QueueService
     */
    private $queueService;

    public function __construct(
        QueueRepositoryInterface $repository,
        QueueService $queueService
    )
    {
        $this->repository = $repository;
        $this->queueService = $queueService;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $collection = $this->repository->getCollection();

        $collection->addFieldToFilter(QueueInterface::IS_PROCESSED, 0);

        foreach ($collection as $queue) {
            $this->queueService->process($queue);
        }
    }
}