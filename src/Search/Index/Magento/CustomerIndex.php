<?php

namespace Mulwi\Search\Index\Magento;

use Mulwi\Search\Index\AbstractIndex;

class CustomerIndex extends AbstractIndex
{
    const IDENTIFIER = 'customer';


    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    public function getEntities($lastEntityId = null, $limit = 100)
    {
        /** @var \Magento\Customer\Model\ResourceModel\Customer\Collection $collection */
        $collection = $this->context->objectManager->create('Magento\Customer\Model\ResourceModel\Customer\Collection');
        $collection->setPageSize($limit);
        $collection->addFieldToFilter('entity_id', ['gt' => $lastEntityId]);

        return $collection;
    }


    public function mapDocument($customer)
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $doc = $this->makeDocument(
            'Customer',
            $this->makeExtId(self::IDENTIFIER, $customer->getId())
        );

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