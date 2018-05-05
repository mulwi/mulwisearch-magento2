<?php

namespace Mulwi\Search\Service;

use Mulwi\Search\Api\Data\QueueInterface;
use Mulwi\Search\Api\Repository\IndexRepositoryInterface;
use Mulwi\Search\Api\Repository\QueueRepositoryInterface;

class QueueService
{
    /**
     * @var QueueRepositoryInterface
     */
    private $repository;

    /**
     * @var IndexRepositoryInterface
     */
    private $indexRepository;

    public function __construct(
        QueueRepositoryInterface $queueRepository,
        IndexRepositoryInterface $indexRepository
    )
    {
        $this->repository = $queueRepository;
        $this->indexRepository = $indexRepository;
    }

    public function updateDocument($entity)
    {
        $this->enqueue($entity, QueueInterface::ACTION_ENSURE);
    }

    public function deleteDocument($entity)
    {
        $this->enqueue($entity, QueueInterface::ACTION_DELETE);
    }

    public function process(QueueInterface $queue)
    {
        try {
            $index = $this->indexRepository->getIndex($queue->getIndex());
            $document = $index->getDocument($queue->getValue());

            $index->updateDocument($document);
            $queue->setIsProcessed(true);
        } catch (\Exception $e) {
            $queue->setRetries($queue->getRetries() + 1);
        }

        $this->repository->save($queue);
    }

    private function enqueue($entity, $action)
    {
        if (!$entity || !is_object($entity)) {
            return;
        }
        /** @var \Magento\Framework\Model\AbstractModel $entity */

        foreach ($this->indexRepository->getIndexes() as $index) {
            $value = $index->getQueueValue($entity);

            if ($value) {
                $queue = $this->repository->create();

                $queue->setIndex($index->getIdentifier())
                    ->setAction($action)
                    ->setValue($value);

                $this->repository->save($queue);
            }
        }
    }
}