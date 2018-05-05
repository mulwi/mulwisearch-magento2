<?php

namespace Mulwi\Search\Index\Magento;

use Mulwi\Search\Api\Data\DocumentInterface;
use Mulwi\Search\Index\AbstractIndex;

class InvoiceIndex extends AbstractIndex
{
    const IDENTIFIER = 'Invoice';

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    public function getQueueValue($entity)
    {
        if ($entity instanceof \Magento\Sales\Model\Order\Invoice) {
            return $entity->getId();
        }
    }

    public function getDocuments($lastId = null)
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Invoice\Collection $collection */
        $collection = $this->context->create('Magento\Sales\Model\ResourceModel\Order\Invoice\Collection');
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
            $this->context->create('Magento\Sales\Model\Order\Invoice')
                ->load($id)
        );
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return DocumentInterface
     */
    private function mapDocument($invoice)
    {
        $doc = $this->context->makeDocument(self::IDENTIFIER, $invoice->getId());

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

        $doc->addRelation(OrderIndex::IDENTIFIER, $invoice->getOrderId());

        if ($invoice->getOrder()->getCustomerId()) {
            $doc->addRelation(CustomerIndex::IDENTIFIER, $invoice->getOrder()->getCustomerId());
        } else {
            $doc->addMeta(
                'Customer',
                $invoice->getOrder()->getCustomerName()
            );
        }

        /** @var \\Magento\Sales\Api\Data\InvoiceItemInterface $item */
        foreach ($invoice->getItems() as $item) {
            $doc->addRelation(ProductIndex::IDENTIFIER, $item->getProductId());
        }

        return $doc;
    }
}