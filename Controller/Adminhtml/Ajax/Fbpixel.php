<?php
/**
 * Copyright (c) Meta Platforms, Inc. and affiliates. All Rights Reserved
 */

namespace Facebook\BusinessExtension\Controller\Adminhtml\Ajax;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Facebook\BusinessExtension\Model\System\Config as SystemConfig;

class Fbpixel extends AbstractAjax
{
    /**
     * @var SystemConfig
     */
    protected $systemConfig;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Facebook\BusinessExtension\Helper\FBEHelper $fbeHelper
     * @param SystemConfig $systemConfig
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Facebook\BusinessExtension\Helper\FBEHelper $fbeHelper,
        SystemConfig $systemConfig
    ) {
        parent::__construct($context, $resultJsonFactory, $fbeHelper);
        $this->systemConfig = $systemConfig;
    }

    // Yet to verify how to use the pii info, hence have commented the part of code.
    public function executeForJson()
    {
        $oldPixelId = $this->systemConfig->getPixelId();
        $response = [
            'success' => false,
            'pixelId' => $oldPixelId
        ];
        $pixelId = $this->getRequest()->getParam('pixelId');
        if ($pixelId && $this->_fbeHelper->isValidFBID($pixelId)) {
            $this->systemConfig->saveConfig(SystemConfig::XML_PATH_FACEBOOK_BUSINESS_EXTENSION_PIXEL_ID, $pixelId);
            $this->systemConfig->saveConfig(SystemConfig::XML_PATH_FACEBOOK_BUSINESS_EXTENSION_INSTALLED, true);
            $response['success'] = true;
            $response['pixelId'] = $pixelId;
            if ($oldPixelId != $pixelId) {
                $this->_fbeHelper->log(sprintf("Pixel id updated from %d to %d", $oldPixelId, $pixelId));
                $datetime = $this->_fbeHelper->createObject(DateTime::class);
                $this->systemConfig->saveConfig(
                    SystemConfig::XML_PATH_FACEBOOK_BUSINESS_EXTENSION_PIXEL_INSTALL_TIME,
                    $datetime->gmtDate('Y-m-d H:i:s')
                );
            }
        }
        return $response;
    }
}
