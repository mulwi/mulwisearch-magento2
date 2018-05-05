<?php

namespace Mulwi\Search\Model;

use Magento\Framework\Model\AbstractModel;
use Mulwi\Search\Api\Data\QueueInterface;

class Queue extends AbstractModel implements QueueInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mulwi\Search\Model\ResourceModel\Queue::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getIndex()
    {
        return $this->getData(self::INDEX);
    }

    /**
     * {@inheritdoc}
     */
    public function setIndex($value)
    {
        return $this->setData(self::INDEX, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAction()
    {
        return $this->getData(self::ACTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setAction($value)
    {
        return $this->setData(self::ACTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->getData(self::VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getRetries()
    {
        return $this->getData(self::RETRIES);
    }

    /**
     * {@inheritdoc}
     */
    public function setRetries($value)
    {
        return $this->setData(self::RETRIES, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function isProcessed()
    {
        return $this->getData(self::IS_PROCESSED);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsProcessed($value)
    {
        return $this->setData(self::IS_PROCESSED, $value);
    }
}