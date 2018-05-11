<?php

namespace Mulwi\Search\Controller\Adminhtml\Document;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Redirect extends Action
{

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $path = $this->getRequest()->getParam('path');
        $params = $this->getRequest()->getParam('params');

        return $this->_redirect($path, $params);
    }

    protected function _isAllowed()
    {
        return true;
    }
}
