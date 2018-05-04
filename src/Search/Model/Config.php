<?php

namespace Mulwi\Search\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class Config
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
    }

    /**
     * @return string
     */
    public function getApplicationDomain()
    {
        return $this->scopeConfig->getValue('mulwi/general/domain');
    }

    /**
     * @return string
     */
    public function getApplicationID()
    {
        return $this->scopeConfig->getValue('mulwi/general/application_id');
    }

    /**
     * @return string
     */
    public function getApplicationKey()
    {

        return $this->encryptor->decrypt(
            $this->scopeConfig->getValue('mulwi/general/application_key')
        );
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->scopeConfig->getValue('mulwi/general/api_url');
    }

    /**
     * @return string
     */
    public function getAppUrl()
    {
        return $this->scopeConfig->getValue('mulwi/general/app_url');
    }
}