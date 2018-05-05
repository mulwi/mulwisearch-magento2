<?php

namespace Mulwi\Search\Api\Repository;

use Mulwi\Search\Api\Data\QueueInterface;

interface QueueRepositoryInterface
{
    /**
     * @return \Mulwi\Search\Model\ResourceModel\Queue\Collection | QueueInterface[]
     */
    public function getCollection();

    /**
     * @return QueueInterface
     */
    public function create();

    /**
     * @param QueueInterface $queue
     * @return QueueInterface
     */
    public function save(QueueInterface $queue);

    /**
     * @param int $id
     * @return QueueInterface|false
     */
    public function get($id);

    /**
     * @param QueueInterface $queue
     * @return bool
     */
    public function delete(QueueInterface $queue);
}