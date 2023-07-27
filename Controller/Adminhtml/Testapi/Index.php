<?php
declare(strict_types=1);
namespace ML\DeveloperTest\Controller\Adminhtml\Testapi;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Controller\Result\JsonFactory;

use ML\DeveloperTest\Helper\GeoIp;

class Index extends Action implements HttpGetActionInterface
{
    private $remoteAddress;
    private $geoIp;
    private $resultJsonFactory;

    public function __construct(
        Action\Context $context,
        RemoteAddress $remoteAddress,
        GeoIp $geoIp,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->remoteAddress     = $remoteAddress;
        $this->geoIp             = $geoIp;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        // Defaults
        $isValid = true;
        $errorMessage = 'API test failed.';

        // Values from input fields
        $apiKey = $this->getRequest()->getParam('api_key');
        $apiUrl = $this->getRequest()->getParam('api_url');
        
        // Get admin user IP address
        $ipAddress = $this->remoteAddress->getRemoteAddress();
       
        // Make request using input fields and users ip
        $result = $this->geoIp->getGeoLocationDataByIp($ipAddress, $apiKey, $apiUrl);

        // Check for unsuccessful request
        if(isset($result['success']) && !$result['success'] || !$result){
            $isValid = false;

            if(isset($result['error']['info'])){
                $errorMessage = $result['error']['info'];
            }
        }

        $message = $isValid ? __('API test successful.') : __($errorMessage);

        // Return the test result as JSON response
        $result = $this->resultJsonFactory->create();
        return $result->setData(['success' => $isValid, 'message' => $message]);
    }
}
