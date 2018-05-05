<?php

namespace Mulwi\Search\Api\Data;

interface IndexInterface
{
    /**
     * @return bool
     */
    public function isAvailable();

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @param string $lastId
     * @return DocumentInterface[]
     */
    public function getDocuments($lastId = null);

    /**
     * @param string $id
     * @return DocumentInterface
     */
    public function getDocument($id);

    /**
     * @param object $entity
     * @return string|null
     */
    public function getQueueValue($entity);

    /**
     * @param DocumentInterface $doc
     * @return bool
     */
    public function updateDocument(DocumentInterface $doc);

    /**
     * @param DocumentInterface $doc
     * @return bool
     */
    public function deleteDocument(DocumentInterface $doc);
}