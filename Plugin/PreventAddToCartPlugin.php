<?php
declare(strict_types=1);
namespace ML\DeveloperTest\Plugin;

use ML\DeveloperTest\Helper\Config;
use ML\DeveloperTest\Helper\BlockCountry;

use Magento\Framework\Message\ManagerInterface as MessageManager;

class PreventAddToCartPlugin
{
    /**
     * @param Config $config
     * @param BlockCountry $blockCountry
     */
    public function __construct(
        Config $config,
        BlockCountry $blockCountry,
        MessageManager $messageManager
    )
    {
        $this->config         = $config;
        $this->blockCountry   = $blockCountry;
        $this->messageManager = $messageManager;
    }

    public function beforeAddProduct(\Magento\Checkout\Model\Cart $cart, $productInfo, $requestInfo = null)
    {
        // Check if geoIp module is enabled in admin config and other setting have been configured
        if($this->blockCountry->checkRequired()){
            
            // Check if the customer's country is blocked from purchasing this product
            if($this->blockCountry->isCustomersCountryBlocked($this->getProductsBlockedCountries($productInfo))){
                throw new \Magento\Framework\Exception\LocalizedException(__($this->blockCountry->getErrorMessage()));
            }
            
        }

    }

    /**
     * Get the list of block countries for a product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getProductsBlockedCountries(\Magento\Catalog\Model\Product $product): array
    {
        $blockedCountries = $product->getBlockCountry() ? explode(',', $product->getBlockCountry()) : [];

        return $blockedCountries;
    }
}
