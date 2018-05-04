<?php

namespace Mulwi\Search\Index;

abstract class AbstractIndex
{
    protected $context;

    public function __construct(
        Context $context
    )
    {
        $this->context = $context;
    }

    public function isAvailable()
    {
        return true;
    }

    public abstract function getIdentifier();

    public function getIndexableClasses()
    {
        return [];
    }

    public abstract function getEntities($lastEntityId = null, $limit = 100);

    /**
     * @param $entity
     * @return Document
     */
    public abstract function mapDocument($entity);

    public function reindexAll()
    {
        if (!$this->isAvailable()) {
            return false;
        }

        $r = $this->context->getClient()
            ->clearIndex($this->getIdentifier());

        $this->context->getClient()->waitTask($r["taskID"]);

        $lastID = 0;
        while (true) {
            $collection = $this->getEntities($lastID);

            if ($collection->count() == 0) {
                break;
            }

            $batch = [];
            foreach ($collection as $entity) {
                $document = $this->mapDocument($entity);
                $document->setSource($this->getSource());

                $lastID = $entity->getId();
                $batch[] = [
                    'action' => 'saveDocument',
                    'body' => [
                        'extID' => $document->getData('extID'),
                        'document' => $document->getData(),
                    ],
                ];
            }
            $this->batch($this->getIdentifier(), $batch);
        }
    }

    public function updateDocument($entity)
    {
        try {
            $document = $this->mapDocument($entity);
            $document->setSource($this->getSource());
            $this->context->getClient()->getIndex($this->getIdentifier())
                ->updateDocument($document->getData('extID'), $document->getData());
        } catch (\Exception $e) {
        }
    }


    public function deleteDocument($entity)
    {
        $document = $this->mapDocument($entity);
        $this->context->getClient()->getIndex($this->getIdentifier())
            ->deleteDocument($document->getData('extID'));
    }

    /**
     * @param string $kind
     * @param string $id
     * @return Document
     */
    protected function makeDocument($kind, $id)
    {
        $doc = new Document();
        $doc->setKind($kind)
            ->setExtID($id);

        return $doc;
    }

    protected function makeExtId($kind, $id)
    {
        return $kind . '_' . $id;
    }

    protected function getSource()
    {
        return 'Magento 2';
    }

    private function batch($index, $batch, $attempt = 1)
    {
        try {
            $this->context->getClient()
                ->getIndex($this->getIdentifier())
                ->batch($batch);
        } catch (\Exception $e) {
            if ($attempt < 5) {
                $this->batch($index, $batch, $attempt + 1);
            }
        }
    }
}