<?php

namespace Mulwi\Search\Api\Data;

interface QueueInterface
{
    const TABLE_NAME = "mulwi_search_queue";

    const ID = "queue_id";
    const INDEX = "index";
    const ACTION = "action";
    const VALUE = "value";
    const RETRIES = "retries";
    const IS_PROCESSED = "is_processed";

    const CREATED_AT = "created_at";
    const UPDATED_AT = "updated_at";

    const ACTION_ENSURE = "ensure";
    const ACTION_DELETE = "delete";

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getIndex();

    /**
     * @param string $value
     * @return $this
     */
    public function setIndex($value);

    /**
     * @return string
     */
    public function getAction();

    /**
     * @param string $value
     * @return $this
     */
    public function setAction($value);

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value);

    /**
     * @return int
     */
    public function getRetries();

    /**
     * @param int $value
     * @return $this
     */
    public function setRetries($value);

    /**
     * @return bool
     */
    public function isProcessed();

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsProcessed($value);
}