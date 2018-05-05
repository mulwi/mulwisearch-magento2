<?php

namespace Mulwi\Search\Index\Magento;

use Mulwi\Search\Index\AbstractIndex;

class CustomerIndex extends AbstractIndex
{
    const IDENTIFIER = 'Customer';

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    public function getQueueValue($entity)
    {
        if ($entity instanceof \Magento\Customer\Model\Customer) {
            return $entity->getId();
        }
    }

    public function getDocuments($lastId = null)
    {
        /** @var \Magento\Customer\Model\ResourceModel\Customer\Collection $collection */
        $collection = $this->context->create('Magento\Customer\Model\ResourceModel\Customer\Collection');
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
            $this->context->create('Magento\Customer\Model\Customer')
                ->load($id)
        );
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @return \Mulwi\Search\Model\Document
     */
    public function mapDocument($customer)
    {
        $doc = $this->context->makeDocument(self::IDENTIFIER, $customer->getId());

        $doc->setTitle($customer->getName())
            ->setUrl($this->context->getUrl('customer/index/edit', ['id' => $customer->getId()]))
            ->setCreatedAt($customer->getCreatedAt());

        $doc->addMeta(
            'Email',
            $customer->getEmail()
        );

        $address = $customer->getPrimaryBillingAddress();
        if ($address) {
            foreach ($address->getAttributes() as $att) {
                if (in_array($att->getAttributeCode(), ['firstname', 'lastname'])) {
                    continue;
                }

                $v = $address->getDataUsingMethod($att->getAttributeCode());
                if (is_array($v)) {
                    $v = implode(' ', $v);
                }

                if ($att->getStoreLabel() && $v) {
                    $doc->addMeta($att->getStoreLabel(), $v);
                }
            }
        }

        return $doc;
    }
}