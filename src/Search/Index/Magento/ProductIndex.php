<?php

namespace Mulwi\Search\Index\Magento;

use Mulwi\Search\Api\Data\DocumentInterface;
use Mulwi\Search\Index\AbstractIndex;

class ProductIndex extends AbstractIndex
{
    const IDENTIFIER = 'Product';


    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    public function getQueueValue($entity)
    {
        if ($entity instanceof \Magento\Catalog\Model\Product) {
            return $entity->getId();
        }
    }

    public function getDocuments($lastId = null)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->context->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
        $collection->setPageSize(100)
            ->addFieldToFilter('entity_id', ['gt' => $lastId])
            ->addAttributeToSelect('name');

        $docs = [];
        foreach ($collection as $entity) {
            $docs[] = $this->mapDocument($entity);
        }

        return $docs;
    }

    public function getDocument($id)
    {
        return $this->mapDocument(
            $this->context->create('Magento\Catalog\Model\Product')
                ->load($id)
        );
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return DocumentInterface
     */
    private function mapDocument($product)
    {
        $doc = $this->context->makeDocument(self::IDENTIFIER, $product->getId());

        $doc->setTitle($product->getName())
            ->setUrl($this->context->getUrl('catalog/product/edit', ['id' => $product->getId()]))
            ->setCreatedAt($product->getCreatedAt());

        $doc->addMeta(
            'SKU',
            $product->getSku()
        )->addMeta(
            'Available',
            $product->isAvailable() ? 'Yes' : 'No'
        );

        foreach ($product->getCategoryIds() as $categoryId) {
            $doc->addRelation(CategoryIndex::IDENTIFIER, $categoryId);
        }

        return $doc;
    }
}