<?php
/**
 * Copyright (c) Meta Platforms, Inc. and affiliates. All Rights Reserved
 */

namespace Facebook\BusinessExtension\Controller\Adminhtml\Ajax;

use Facebook\BusinessExtension\Model\Product\Feed\Method\BatchApi;
use Facebook\BusinessExtension\Model\System\Config as SystemConfig;

class PersistConfiguration extends AbstractAjax
{
    /**
     * @var BatchApi
     */
    protected $batchApi;
    /**
     * @var SystemConfig
     */
    protected $systemConfig;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Facebook\BusinessExtension\Helper\FBEHelper $helper
     * @param BatchApi $batchApi
     * @param SystemConfig $systemConfig
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Facebook\BusinessExtension\Helper\FBEHelper $helper,
        BatchApi $batchApi,
        SystemConfig $systemConfig
    ) {
        parent::__construct($context, $resultJsonFactory, $helper);
        $this->batchApi = $batchApi;
        $this->systemConfig = $systemConfig;
    }

    public function executeForJson()
    {
        try {
            $externalBusinessId = $this->getRequest()->getParam('externalBusinessId');
            $this->saveExternalBusinessId($externalBusinessId);
            $catalogId = $this->getRequest()->getParam('catalogId');
            $this->saveCatalogId($catalogId);
            $response['success'] = true;
            $response['feed_push_response'] = 'Business and catalog IDs successfully saved';
            return $response;
        } catch (\Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
            $this->_fbeHelper->logException($e);
            return $response;
        }
    }

    /**
     * @param $catalogId
     * @return $this
     */
    public function saveCatalogId($catalogId)
    {
        if ($catalogId != null) {
            $this->systemConfig->saveConfig(SystemConfig::XML_PATH_FACEBOOK_BUSINESS_EXTENSION_CATALOG_ID, $catalogId);
            $this->_fbeHelper->log("Catalog id saved on instance --- ". $catalogId);
        }
        return $this;
    }

    /**
     * @param $externalBusinessId
     * @return $this
     */
    public function saveExternalBusinessId($externalBusinessId)
    {
        if ($externalBusinessId != null) {
            $this->systemConfig->saveConfig(SystemConfig::XML_PATH_FACEBOOK_BUSINESS_EXTENSION_EXTERNAL_BUSINESS_ID, $externalBusinessId);
            $this->_fbeHelper->log("External business id saved on instance --- ". $externalBusinessId);
        }
        return $this;
    }
}
