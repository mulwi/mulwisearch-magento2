<?php

namespace Mulwi\Search\Model\ResourceModel\Queue;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Mulwi\Search\Model\Queue::class,
            \Mulwi\Search\Model\ResourceModel\Queue::class
        );
    }
}
