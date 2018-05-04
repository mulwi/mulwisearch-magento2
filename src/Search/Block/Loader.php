<?php

namespace Mulwi\Search\Block;

use Magento\Backend\Block\Template;
use Mulwi\Search\Model\Config;

class Loader extends Template
{
    protected $_template = "Mulwi_Search::loader.phtml";

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Config $config,
        Template\Context $context,
        array $data = []
    )
    {
        $this->config = $config;

        parent::__construct($context, $data);
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    public function getAppUrl()
    {
        return "//" . $this->config->getApplicationDomain() . '.' . $this->config->getAppUrl();
    }

    public function getRedirectUrl()
    {
        return $this->_urlBuilder->getUrl('mulwi/document/redirect');
    }
}