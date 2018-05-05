<?php

namespace Mulwi\Search\Repository;

use Mulwi\Search\Api\Repository\QueueRepositoryInterface;
use Magento\Framework\EntityManager\EntityManager;
use Mulwi\Search\Api\Data\QueueInterface;
use Mulwi\Search\Model\ResourceModel\Queue\CollectionFactory;
use Mulwi\Search\Api\Data\QueueInterfaceFactory;

class QueueRepository implements QueueRepositoryInterface
{
    /**
     * @var QueueInterfaceFactory
     */
    private $factory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(
        EntityManager $entityManager,
        QueueInterfaceFactory $factory,
        CollectionFactory $collectionFactory
    )
    {
        $this->entityManager = $entityManager;
        $this->factory = $factory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $queue = $this->create();

        $this->entityManager->load($queue, $id);

        return $queue->getId() ? $queue : false;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(QueueInterface $queue)
    {
        return $this->entityManager->delete($queue);
    }

    /**
     * {@inheritdoc}
     */
    public function save(QueueInterface $queue)
    {
        return $this->entityManager->save($queue);
    }
}