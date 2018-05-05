<?php

namespace Mulwi\Search\Index;

use Mulwi\Search\Api\Data\DocumentInterface;
use Mulwi\Search\Api\Data\IndexInterface;

abstract class AbstractIndex implements IndexInterface
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

    public function reindexAll()
    {
        if (!$this->isAvailable()) {
            return false;
        }

        $r = $this->context->getClient()
            ->clearIndex($this->getIndexName());

        $this->context->getClient()->waitTask($r["taskID"]);

        $lastID = 0;
        while (true) {
            $documents = $this->getDocuments($lastID);

            if (count($documents) == 0) {
                break;
            }

            $batch = [];
            foreach ($documents as $doc) {
                $lastID = $doc->getId();
                $batch[] = [
                    'action' => 'saveDocument',
                    'body' => [
                        'extID' => $doc->getData('extID'),
                        'document' => $doc->getData(),
                    ],
                ];
            }

            $this->batch($this->getIdentifier(), $batch);
        }
    }


    public function updateDocument(DocumentInterface $document)
    {
        $this->context->getClient()->getIndex($this->getIndexName())
            ->updateDocument($document->getData('extID'), $document->getData());
    }

    public function deleteDocument(DocumentInterface $document)
    {
        $this->context->getClient()->getIndex($this->getIndexName())
            ->deleteDocument($document->getData('extID'));
    }

    private function batch($index, $batch, $attempt = 1)
    {
        try {
            $this->context->getClient()
                ->getIndex($this->getIndexName())
                ->batch($batch);
        } catch (\Exception $e) {
            if ($attempt < 5) {
                $this->batch($index, $batch, $attempt + 1);
            }
        }
    }

    private function getIndexName()
    {
        return preg_replace("/[^a-z0-9]/", '', strtolower($this->getIdentifier()));
    }

}