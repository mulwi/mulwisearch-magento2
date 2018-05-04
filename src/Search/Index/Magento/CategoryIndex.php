<?php

namespace Mulwi\Search\Index\Magento;

use Mulwi\Search\Index\AbstractIndex;

class CategoryIndex extends AbstractIndex
{
    const IDENTIFIER = 'category';


    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    public function getEntities($lastEntityId = null, $limit = 100)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->context->objectManager->create('Magento\Catalog\Model\ResourceModel\Category\Collection');
        $collection->setPageSize($limit);
        $collection->addFieldToFilter('entity_id', ['gt' => $lastEntityId]);
        $collection->addAttributeToSelect('name');

        return $collection;
    }


    public function mapDocument($category)
    {
        /** @var \Magento\Catalog\Model\Category $category */

        $doc = $this->makeDocument(
            'Category',
            $this->makeExtId(CategoryIndex::IDENTIFIER, $category->getId())
        );

        $doc->setTitle($category->getName())
            ->setUrl($this->context->getUrl('catalog/category/edit', ['id' => $category->getId()]))
            ->setCreatedAt($category->getCreatedAt());

        foreach ($category->getParentIds() as $id) {
            $doc->addRelation(
                $this->getSource(),
                $this->makeExtId(CategoryIndex::IDENTIFIER, $id)
            );
        }

        return $doc;
    }
}