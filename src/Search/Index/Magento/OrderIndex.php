<?php

namespace Mulwi\Search\Index\Magento;

use Mulwi\Search\Index\AbstractIndex;

class OrderIndex extends AbstractIndex
{
    const IDENTIFIER = 'order';

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    public function getEntities($lastEntityId = null, $limit = 100)
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $collection */
        $collection = $this->context->objectManager->create('Magento\Sales\Model\ResourceModel\Order\Collection');
        $collection->setPageSize($limit);
        $collection->addFieldToFilter('entity_id', ['gt' => $lastEntityId]);

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function mapDocument($order)
    {
        /** @var \Magento\Sales\Model\Order $order */

        $doc = $this->makeDocument(
            'Order',
            $this->makeExtId(self::IDENTIFIER, $order->getId())
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
                $this->getSource(),
                $this->makeExtId(CustomerIndex::IDENTIFIER, $order->getCustomerId())
            );
        } else {
            $doc->addMeta(
                'Customer',
                $order->getCustomerName()
            );
        }

        $doc->addContent($order->getCustomerName());
        $doc->addContent($order->getCustomerEmail());

        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach ($order->getAllVisibleItems() as $item) {
            $doc->addRelation(
                $this->getSource(),
                $this->makeExtId(ProductIndex::IDENTIFIER, $item->getProductId())
            );
        }

        return $doc;
    }
}