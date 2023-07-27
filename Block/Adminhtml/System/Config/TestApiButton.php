<?php
declare(strict_types=1);
namespace ML\DeveloperTest\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;

class TestApiButton extends Field
{
    protected $urlBuilder;

    /**
     * @param Context $context
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $data);
    }

    /**
     * Render button
     *
     * @param  AbstractElement $element
     * @return string
     * @throws LocalizedException
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        // Generate the button HTML
        $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->setData([
            'id' => 'test_api_button',
            'label' => __('Test API Details'),
        ]);

        $block = $this->_layout->createBlock('\ML\DeveloperTest\Block\Adminhtml\System\Config\TestApiButton');

        $block->setTemplate('ML_DeveloperTest::system/config/test_api_button.phtml')
            ->setChild('button', $button)
            ->setData('select_html', parent::_getElementHtml($element));

        return $block->toHtml();
    }

    public function getAjaxCheckUrl(): string
    {
        return $this->urlBuilder->getUrl('testapi/testapi');
    }
}
