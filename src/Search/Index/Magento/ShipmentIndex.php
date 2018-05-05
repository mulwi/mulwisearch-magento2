<?php

namespace Mulwi\Search\Index\Magento;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Mulwi\Search\Index\AbstractIndex;
use Mulwi\Search\Index\Context;

class ShipmentIndex extends AbstractIndex
{
    const IDENTIFIER = 'Shipment';

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    public function getQueueValue($entity)
    {
        if ($entity instanceof \Magento\Sales\Model\Order\Shipment) {
            return $entity->getId();
        }
    }

    public function getDocuments($lastId = null)
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection $collection */
        $collection = $this->context->create('Magento\Sales\Model\ResourceModel\Order\Shipment\Collection');
        $collection->setPageSize(100)
            ->addFieldToFilter('entity_id', ['gt' => $lastId]);

        $docs = [];
        foreach ($collection as $entity) {
            $docs[] = $this->mapDocument($entity);
        }

        return $docs;
    }

    public function getDocument($id)
    {
        return $this->mapDocument(
            $this->context->create('Magento\Sales\Model\Order\Shipment')
                ->load($id)
        );
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @return \Mulwi\Search\Model\Document
     */
    private function mapDocument($shipment)
    {
        $doc = $this->context->makeDocument(self::IDENTIFIER, $shipment->getId());

        $doc->setTitle('Shipment #' . $shipment->getIncrementId())
            ->setUrl($this->context->getUrl('sales/shipment/view', ['shipment_id' => $shipment->getId()]))
            ->setCreatedAt($shipment->getCreatedAt());


        $doc->addRelation(OrderIndex::IDENTIFIER, $shipment->getOrderId());
        $doc->addRelation(CustomerIndex::IDENTIFIER, $shipment->getCustomerId());

        /** @var \Magento\Sales\Api\Data\ShipmentItemInterface $item */
        foreach ($shipment->getItems() as $item) {
            $doc->addRelation(ProductIndex::IDENTIFIER, $item->getProductId());
        }

        return $doc;
    }
}