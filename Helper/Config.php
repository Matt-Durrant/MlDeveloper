<?php
declare(strict_types=1);
namespace ML\DeveloperTest\Helper;

use \Magento\Framework\App\Config\ScopeConfigInterface;

class Config 
{

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get system config values
     *
     * @param string $code
     * @param integer $storeId
     * @return mixed
     */
    public function getAdminConfig(string $code, int $storeId = 0): mixed
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        return $this->scopeConfig->getValue($code, $storeScope, $storeId);
    }

}
