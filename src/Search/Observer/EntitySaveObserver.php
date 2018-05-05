<?php

namespace Mulwi\Search\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class EntitySaveObserver extends AbstractObserver implements ObserverInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\Model\AbstractModel $object */
        $object = $observer->getEntity();

        $this->queueService->updateDocument($object);
    }
}
