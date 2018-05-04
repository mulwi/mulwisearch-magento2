<?php

namespace Mulwi\Search\Index\Magento;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Mulwi\Search\Index\AbstractIndex;
use Mulwi\Search\Index\Context;

class ShipmentIndex extends AbstractIndex
{
    const IDENTIFIER = 'shipment';

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    public function getEntities($lastEntityId = null, $limit = 100)
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection $collection */
        $collection = $this->context->objectManager->create('Magento\Sales\Model\ResourceModel\Order\Shipment\Collection');
        $collection->setPageSize($limit);
        $collection->addFieldToFilter('entity_id', ['gt' => $lastEntityId]);

        return $collection;
    }

    public function mapDocument($shipment)
    {
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */

        $doc = $this->makeDocument(
            'Shipment',
            $this->makeExtId(self::IDENTIFIER, $shipment->getId())
        );

        $doc->setTitle('Shipment #' . $shipment->getIncrementId())
            ->setUrl($this->context->getUrl('sales/shipment/view', ['shipment_id' => $shipment->getId()]))
            ->setCreatedAt($shipment->getCreatedAt());


        $doc->addRelation(
            $this->getSource(),
            $this->makeExtId(OrderIndex::IDENTIFIER, $shipment->getOrderId())
        );

        $doc->addRelation(
            $this->getSource(),
            $this->makeExtId(CustomerIndex::IDENTIFIER, $shipment->getCustomerId())
        );

        /** @var \Magento\Sales\Api\Data\ShipmentItemInterface $item */
        foreach ($shipment->getItems() as $item) {
            $doc->addRelation(
                $this->getSource(),
                $this->makeExtId(ProductIndex::IDENTIFIER, $item->getProductId())
            );
        }

        return $doc;
    }
}