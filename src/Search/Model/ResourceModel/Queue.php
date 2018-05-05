<?php

namespace Mulwi\Search\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Mulwi\Search\Api\Data\QueueInterface;

class Queue extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(QueueInterface::TABLE_NAME, QueueInterface::ID);
    }
}
