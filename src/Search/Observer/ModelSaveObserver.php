<?php

namespace Mulwi\Search\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ModelSaveObserver extends AbstractObserver implements ObserverInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\Model\AbstractModel $object */
        $object = $observer->getObject();

        $this->queueService->updateDocument($object);
    }
}
