<?php
declare(strict_types=1);
namespace ML\DeveloperTest\Controller\Api;

use Magento\Framework\App\Action\HttpGetActionInterface;

use ML\DeveloperTest\Helper\BlockCountry;

class Test implements HttpGetActionInterface
{
    private $blockCountry;

    public function __construct(
        BlockCountry $blockCountry
    ) {
        $this->blockCountry = $blockCountry;
    }

    public function execute()
    {      
        $blockedCountries = ['FR', 'GB'];

        $result = $this->blockCountry->isCustomersCountryBlocked($blockedCountries);

        dd($result);
    }
}
