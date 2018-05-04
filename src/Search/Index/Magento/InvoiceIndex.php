<?php

namespace Mulwi\Search\Index\Magento;

use Mulwi\Search\Index\AbstractIndex;

class InvoiceIndex extends AbstractIndex
{
    const IDENTIFIER = 'invoice';

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    public function isAvailable()
    {
        return true;
    }

    public function getEntities($lastEntityId = null, $limit = 100)
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Invoice\Collection $collection */
        $collection = $this->context->objectManager->create('Magento\Sales\Model\ResourceModel\Order\Invoice\Collection');
        $collection->setPageSize($limit);
        $collection->addFieldToFilter('entity_id', ['gt' => $lastEntityId]);

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function mapDocument($invoice)
    {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */

        $doc = $this->makeDocument(
            'Invoice',
            $this->makeExtId(self::IDENTIFIER, $invoice->getId())
        );

        $doc->setTitle('Invoice #' . $invoice->getIncrementId())
            ->setUrl($this->context->getUrl('sales/invoice/view', ['invoice_id' => $invoice->getId()]))
            ->setCreatedAt($invoice->getCreatedAt());

        $doc->addMeta(
            'Grand Total',
            $this->context->priceCurrency->format($invoice->getGrandTotal(), false)
        );

        if ($invoice->getDiscountAmount() < 0) {
            $doc->addMeta(
                'Discount',
                $this->context->priceCurrency->format($invoice->getDiscountAmount(), false)
            );
        }

        if ($invoice->getShippingAmount() > 0) {
            $doc->addMeta(
                'Shipping',
                $this->context->priceCurrency->format($invoice->getShippingAmount(), false)
            );
        }

        if ($invoice->getTaxAmount() > 0) {
            $doc->addMeta(
                'Tax',
                $this->context->priceCurrency->format($invoice->getTaxAmount(), false)
            );
        }

        $doc->addRelation(
            $this->getSource(),
            $this->makeExtId(OrderIndex::IDENTIFIER, $invoice->getOrderId())
        );

        if ($invoice->getOrder()->getCustomerId()) {
            $doc->addRelation(
                $this->getSource(),
                $this->makeExtId(CustomerIndex::IDENTIFIER, $invoice->getOrder()->getCustomerId())
            );
        } else {
            $doc->addMeta(
                'Customer',
                $invoice->getOrder()->getCustomerName()
            );
        }

        /** @var \\Magento\Sales\Api\Data\InvoiceItemInterface $item */
        foreach ($invoice->getItems() as $item) {
            $doc->addRelation(
                $this->getSource(),
                $this->makeExtId(ProductIndex::IDENTIFIER, $item->getProductId())
            );
        }

        return $doc;
    }
}