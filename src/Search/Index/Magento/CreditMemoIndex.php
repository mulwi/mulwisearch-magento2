<?php

namespace Mulwi\Search\Index\Magento;

use Mulwi\Search\Index\AbstractIndex;

class CreditMemoIndex extends AbstractIndex
{
    const IDENTIFIER = 'credit_memo';

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    public function getEntities($lastEntityId = null, $limit = 100)
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection $collection */
        $collection = $this->context->objectManager->create('Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection');
        $collection->setPageSize($limit);
        $collection->addFieldToFilter('entity_id', ['gt' => $lastEntityId]);

        return $collection;
    }

    public function mapDocument($memo)
    {
        /** @var \Magento\Sales\Model\Order\Creditmemo $memo */

        $doc = $this->makeDocument(
            'Credit Memo',
            $this->makeExtId(self::IDENTIFIER, $memo->getId())
        );


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

        $doc->addRelation(
            $this->getSource(),
            $this->makeExtId(OrderIndex::IDENTIFIER, $memo->getOrderId())
        );

        $doc->addRelation(
            $this->getSource(),
            $this->makeExtId(InvoiceIndex::IDENTIFIER, $memo->getInvoiceId())
        );

        if ($memo->getOrder()->getCustomerId()) {
            $doc->addRelation(
                $this->getSource(),
                $this->makeExtId(CustomerIndex::IDENTIFIER, $memo->getOrder()->getCustomerId())
            );
        } else {
            $doc->addMeta(
                'Customer',
                $memo->getOrder()->getCustomerName()
            );
        }

        /** @var \Magento\Sales\Api\Data\CreditMemoItemInterface $item */
        foreach ($memo->getItems() as $item) {
            $doc->addRelation(
                $this->getSource(),
                $this->makeExtId(ProductIndex::IDENTIFIER, $item->getProductId())
            );
        }

        return $doc;
    }
}