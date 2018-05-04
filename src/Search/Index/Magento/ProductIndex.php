<?php

namespace Mulwi\Search\Index\Magento;

use Mulwi\Search\Index\AbstractIndex;

class ProductIndex extends AbstractIndex
{
    const IDENTIFIER = 'product';


    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    public function getIndexableClasses()
    {
        return [
            \Magento\Catalog\Model\Product::class,
        ];
    }

    public function getEntities($lastEntityId = null, $limit = 100)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->context->objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection');

        $collection->setPageSize($limit);
        $collection->addFieldToFilter('entity_id', ['gt' => $lastEntityId]);
        $collection->addAttributeToSelect('name');

        return $collection;
    }


    public function mapDocument($product)
    {
        /** @var \Magento\Catalog\Model\Product $product */

        $doc = $this->makeDocument(
            'Product',
            $this->makeExtId(self::IDENTIFIER, $product->getId())
        );


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
            $doc->addRelation(
                $this->getSource(),
                $this->makeExtId(CategoryIndex::IDENTIFIER, $categoryId)
            );
        }

        return $doc;
    }
}