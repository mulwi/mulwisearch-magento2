<?php

namespace Mulwi\Search\Index\Magento;

use Mulwi\Search\Index\AbstractIndex;

class CategoryIndex extends AbstractIndex
{
    const IDENTIFIER = 'Category';


    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    public function getQueueValue($entity)
    {
        if ($entity instanceof \Magento\Catalog\Model\Category) {
            return $entity->getId();
        }
    }

    public function getDocuments($lastId = null)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->context->create('Magento\Catalog\Model\ResourceModel\Category\Collection');
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
            $this->context->create('Magento\Catalog\Model\Category')
                ->load($id)
        );
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return \Mulwi\Search\Model\Document
     */
    public function mapDocument($category)
    {
        $doc = $this->context->makeDocument(CategoryIndex::IDENTIFIER, $category->getId());

        $doc->setTitle($category->getName())
            ->setUrl($this->context->getUrl('catalog/category/edit', ['id' => $category->getId()]))
            ->setCreatedAt($category->getCreatedAt());

        foreach ($category->getParentIds() as $id) {
            $doc->addRelation(CategoryIndex::IDENTIFIER, $id);
        }

        return $doc;
    }
}