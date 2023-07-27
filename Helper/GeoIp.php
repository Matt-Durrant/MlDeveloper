<?php
declare(strict_types=1);
namespace ML\DeveloperTest\Helper;

use Magento\Framework\HTTP\Client\Curl;

use Psr\Log\LoggerInterface;

use ML\DeveloperTest\Helper\Config;

class GeoIp 
{

    /**
     * @var Curl
     */
    private $curl;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Curl $curl
     * @param LoggerInterface $logger
     * @param Config $config
     */
    public function __construct(
        Curl $curl,
        LoggerInterface $logger,
        Config $config
    )
    {
        $this->curl   = $curl;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * Get visitors geo location data by their IP address
     *
     * @param string $ipAddress
     * @param string|null $apiKey
     * @param string|null $baseUrl
     * @return array|null
     */
    public function getGeoLocationDataByIp(string $ipAddress, ?string $apiKey = null, ?string $baseUrl = null): ?array
    {
        // If no api key provided get it from admin config
        if(!$apiKey){
            $apiKey = $this->config->getAdminConfig('geoip/general/api_key');
        }
        
        // If no base url provided get it from admin config
        if(!$baseUrl){
            $baseUrl = $this->config->getAdminConfig('geoip/general/api_url');
        }
        
        try {
            // API endpoint for ip geolocation lookup
            $url = "{$baseUrl}{$ipAddress}?access_key={$apiKey}";

            // Send HTTP GET request to the API
            $this->curl->get($url);

            // Get the API response
            $response = $this->curl->getBody();

            // Parse the JSON response
            $data = json_decode($response, true);

            // Check for unsuccessful requests and log error message
            if(isset($data['success']) && !$data['success'] && isset($data['error']['info'])){
                $this->logger->error($data['error']['info']);
            }
            
            return $data;

        } catch (\Exception $exception) {
            // Log to system.log
            $this->logger->error($exception);
        }

        return null;
    }

}
