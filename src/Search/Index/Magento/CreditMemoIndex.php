<?php

namespace Mulwi\Search\Index\Magento;

use Mulwi\Search\Index\AbstractIndex;

class CreditMemoIndex extends AbstractIndex
{
    const IDENTIFIER = 'Credit Memo';

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    public function getQueueValue($entity)
    {
        if ($entity instanceof \Magento\Sales\Model\Order\Creditmemo) {
            return $entity->getId();
        }
    }

    public function getDocuments($lastId = null)
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection $collection */
        $collection = $this->context->create('Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection');
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
            $this->context->create('Magento\Sales\Model\Order\Creditmemo')
                ->load($id)
        );
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $memo
     * @return \Mulwi\Search\Model\Document
     */
    public function mapDocument($memo)
    {
        $doc = $this->context->makeDocument(self::IDENTIFIER, $memo->getId());

        $doc->setTitle('Credit Memo #' . $memo->getIncrementId())
            ->setUrl($this->context->getUrl('sales/creditmemo/view', ['creditmemo_id' => $memo->getId()]))
            ->setCreatedAt($memo->getCreatedAt());

        $doc->addMeta(
            'Grand Total',
            $this->context->priceCurrency->format($memo->getGrandTotal(), false)
        );

        if ($memo->getDiscountAmount() < 0) {
            $doc->addMeta(
                'Discount',
                $this->context->priceCurrency->format($memo->getDiscountAmount(), false)
            );
        }

        if ($memo->getShippingAmount() > 0) {
            $doc->addMeta(
                'Shipping',
                $this->context->priceCurrency->format($memo->getShippingAmount(), false)
            );
        }

        if ($memo->getTaxAmount() > 0) {
            $doc->addMeta(
                'Tax',
                $this->context->priceCurrency->format($memo->getTaxAmount(), false)
            );
        }

        $doc->addRelation(OrderIndex::IDENTIFIER, $memo->getOrderId());

        $doc->addRelation(InvoiceIndex::IDENTIFIER, $memo->getInvoiceId());

        if ($memo->getOrder()->getCustomerId()) {
            $doc->addRelation(CustomerIndex::IDENTIFIER, $memo->getOrder()->getCustomerId());
        } else {
            $doc->addMeta(
                'Customer',
                $memo->getOrder()->getCustomerName()
            );
        }

        /** @var \Magento\Sales\Api\Data\CreditMemoItemInterface $item */
        foreach ($memo->getItems() as $item) {
            $doc->addRelation(ProductIndex::IDENTIFIER, $item->getProductId());
        }

        return $doc;
    }
}