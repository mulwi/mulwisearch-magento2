<?php

namespace Mulwi\Search\Index;

use Magento\Backend\Model\UrlInterface;
use Mulwi\Search\Model\Config;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Module\ModuleListInterface;
use Mulwi\Search\Model\Document;

class Context
{
    /**
     * @var \Mulwi\Client
     */
    private $client;

    /**
     * @var PriceCurrencyInterface
     */
    public $priceCurrency;

    /**
     * @var ObjectManagerInterface
     */
    public $objectManager;

    /**
     * @var ModuleListInterface
     */
    public $moduleList;

    /**
     * @var UrlInterface
     */
    public $urlBuilder;

    public function __construct(
        Config $config,
        ObjectManagerInterface $objectManager,
        ModuleListInterface $moduleList,
        PriceCurrencyInterface $priceCurrency,
        UrlInterface $urlBuilder
    )
    {
        $this->client = new \Mulwi\Client(
            $config->getApplicationID(),
            $config->getApiKey(),
            $config->getApiUrl()
        );

        $this->objectManager = $objectManager;
        $this->moduleList = $moduleList;
        $this->priceCurrency = $priceCurrency;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return \Mulwi\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param string $class
     * @return object
     */
    public function create($class)
    {
        return $this->objectManager->create($class);
    }

    /**
     * @param string$kind
     * @param string $id
     * @return Document
     */
    public function makeDocument($kind, $id)
    {
        $doc = new Document();
        $doc->setKind($kind)
            ->setId($id)
            ->setExtID($kind . '_' . $id);

        return $doc;
    }

    /**
     * @param string $path
     * @param array $args
     * @return string
     */
    public function getUrl($path, $args)
    {
        $q = http_build_query([
            'path' => $path,
            'params' => $args,
        ]);

        return $this->urlBuilder->getUrl('mulwi/document/redirect', ['_query' => $q]);
    }
}