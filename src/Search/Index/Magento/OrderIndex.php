<?php

namespace Mulwi\Search\Index\Magento;

use Mulwi\Search\Api\Data\DocumentInterface;
use Mulwi\Search\Index\AbstractIndex;

class OrderIndex extends AbstractIndex
{
    const IDENTIFIER = 'Order';

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    public function getQueueValue($entity)
    {
        if ($entity instanceof \Magento\Sales\Model\Order) {
            return $entity->getId();
        }

        return false;
    }

    public function getDocuments($lastId = null)
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $collection */
        $collection = $this->context->create('Magento\Sales\Model\ResourceModel\Order\Collection');

        $collection->setPageSize(100)
            ->addFieldToFilter('entity_id', ['gt' => intval($lastId)]);

        $docs = [];
        foreach ($collection as $entity) {
            $docs[] = $this->mapDocument($entity);
        }

        return $docs;
    }

    public function getDocument($id)
    {
        return $this->mapDocument(
            $this->context->create('Magento\Sales\Model\Order')
                ->load($id)
        );
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return DocumentInterface
     */
    private function mapDocument($order)
    {
        $doc = $this->context->makeDocument(
            self::IDENTIFIER,
            $order->getId()
        );

        $doc->setTitle('Order #' . $order->getIncrementId())
            ->setUrl($this->context->getUrl('sales/order/view', ['order_id' => $order->getId()]))
            ->setCreatedAt($order->getCreatedAt());

        $doc->addMeta(
            'Status',
            $order->getStatusLabel() . ""
        )->addMeta(
            'Grand Total',
            $this->context->priceCurrency->format($order->getGrandTotal(), false)
        );

        if ($order->getDiscountAmount() < 0) {
            $doc->addMeta(
                'Discount',
                $this->context->priceCurrency->format($order->getDiscountAmount(), false)
            );
        }

        if ($order->getShippingAmount() > 0) {
            $doc->addMeta(
                'Shipping',
                $this->context->priceCurrency->format($order->getShippingAmount(), false)
            );
        }

        if ($order->getTaxAmount() > 0) {
            $doc->addMeta(
                'Tax',
                $this->context->priceCurrency->format($order->getTaxAmount(), false)
            );
        }

        if ($order->getCustomerId()) {
            $doc->addRelation(
                CustomerIndex::IDENTIFIER,
                $order->getCustomerId()
            );
        } else {
            $doc->addMeta(
                'Customer',
                $order->getCustomerName()
            );
        }

        $doc->addSearchable($order->getCustomerName())
            ->addSearchable($order->getCustomerEmail());

        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach ($order->getAllVisibleItems() as $item) {
            $doc->addRelation(
                ProductIndex::IDENTIFIER,
                $item->getProductId()
            );
        }

        return $doc;
    }
}