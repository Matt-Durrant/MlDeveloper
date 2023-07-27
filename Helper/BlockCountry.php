<?php
declare(strict_types=1);
namespace ML\DeveloperTest\Helper;

use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManager;

use Magento\Directory\Model\CountryFactory;

use ML\DeveloperTest\Helper\Config;
use ML\DeveloperTest\Helper\GeoIp;

class BlockCountry 
{

    private $remoteAddress;
    private $countryFactory;
    private $messageManager;
    private $config;
    private $geoIp;
    private $errorMessage;

    /**
     * @param RemoteAddress $remoteAddress
     * @param CountryFactory $countryFactory
     * @param MessageManager $messageManager
     * @param Config $config
     * @param GeoIp $geoIp
     */
    public function __construct(
        RemoteAddress $remoteAddress,
        CountryFactory $countryFactory,
        MessageManager $messageManager,
        Config $config,
        GeoIp $geoIp
    )
    {
        $this->remoteAddress     = $remoteAddress;
        $this->countryFactory    = $countryFactory;
        $this->messageManager    = $messageManager; 
        $this->config            = $config;
        $this->geoIp             = $geoIp;
    }

    /**
     * Check that the module is enabled and the API key and url have been configured
     *
     * @return boolean
     */
    public function checkRequired(): bool
    {
        if(
            $this->config->getAdminConfig('geoip/general/enable') &&
            !empty($this->config->getAdminConfig('geoip/general/api_key')) &&
            !empty($this->config->getAdminConfig('geoip/general/api_url'))
        ){
            return true;
        }

        return false;
    }

    /**
     * Check if the current customer's country is listed in the products block country list
     *
     * @param array $productsBlockedCountries
     * @return boolean
     */
    public function isCustomersCountryBlocked(array $productsBlockedCountries): bool
    {
        $usersCountryCode = $this->getCustomersCountryCode();

        // Check if users country is blocked from purchasing product
        if($productsBlockedCountries && in_array($usersCountryCode, $productsBlockedCountries)){
            $this->createErrorMessage($this->getCountryNameByCode($usersCountryCode));
            return true;
        }
        
        return false;
    }

    /**
     * Get customers country code using their IP address
     *
     * @return string|null
     */
    public function getCustomersCountryCode(): ?string
    {
        // Get user's IP address
        $ipAddress = $this->remoteAddress->getRemoteAddress();
       
        // Get user's geo location data using their IP address
        $geoLocationData = $this->geoIp->getGeoLocationDataByIp($ipAddress);
        
        // Check if the response contains a valid country code
        if (isset($geoLocationData['country_code']) && !empty($geoLocationData['country_code'])) {
            return $geoLocationData['country_code'];
        }

        return null;
    }

    /**
     * Get country name from country code.
     *
     * @param string $countryCode
     * @return string|null
     */
    public function getCountryNameByCode($countryCode)
    {
        try {
            $countryModel = $this->countryFactory->create();
            $countryModel->loadByCode($countryCode);
            return $countryModel->getName();
        } catch (LocalizedException $e) {
            // Handle the exception if needed.
            return null;
        }
    }

    /**
     * Create Error message to be displayed to customer
     *
     * @param string $countryName
     * @return void
     */
    public function createErrorMessage(string $countryName)
    {
        // Get configurable error message from admin config
        $errorMessage = $this->config->getAdminConfig('geoip/general/error_message');

        $this->setErrorMessage("$errorMessage $countryName");
    }

    /**
     * Set Error message
     *
     * @param ?string $message
     * @return void
     */
    public function setErrorMessage(?string $message)
    {
        $this->errorMessage = $message;
    }

    /**
     * Get Error message to be displayed to customer
     *
     * @return ?string
     */
    public function getErrorMessage(?bool $clear = true): ?string
    {
        $message = $this->errorMessage;

        // Clear the message after its been retrieved
        if($clear){
            $this->setErrorMessage(null);
        }

        return $message;
    }

}
