<?php
/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Controller\Payment;

use Magento\Framework\View\Result\PageFactory;
use Kevwis\Atos\Helper\DataFactory;
use Kevwis\Atos\Model\ApiFactory;
use Kevwis\Atos\Model\Order\Email\Sender\Error;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\OrderRepository;
use Psr\Log\LoggerInterface;

/**
 * DirectPost Payment Controller
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class AbstractController extends Action
{

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var DataFactory
     */
    protected $_dataFactory;

    /**
     * @var ApiFactory
     */
    protected $_apiFactory;

    /**
     * @var OrderRepository
     */
    protected $_orderRepository;

    /**
     * @var OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var
     */
    protected $_order;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var Error
     */
    protected $_errorSender;

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentData;

    /**
     * AbstractController constructor.
     * @param Error $errorSender
     * @param Context $context
     * @param Registry $coreRegistry
     * @param OrderRepository $orderRepository
     * @param OrderFactory $orderFactory
     * @param DataFactory $dataFactory
     * @param ApiFactory $apiFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Error $errorSender,
        Context $context,
        Registry $coreRegistry,
        OrderRepository $orderRepository,
        OrderFactory $orderFactory,
        \Kevwis\Atos\Helper\DataFactory $dataFactory,
        \Kevwis\Atos\Model\ApiFactory $apiFactory,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger,
        PageFactory $resultPageFactory,
        \Magento\Payment\Helper\Data $paymentData
    ) {
        $this->_orderRepository = $orderRepository;
        $this->_orderFactory = $orderFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_dataFactory = $dataFactory;
        $this->_apiFactory = $apiFactory;
        $this->_logger = $logger;
        $this->_scopeConfig = $scopeConfig;
        $this->_errorSender = $errorSender;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_paymentData = $paymentData;

        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _isEnabled() {
        return (bool) $this->_scopeConfig->getValue('atos/settings/enabled');
    }

    /**
     * @return \Magento\Checkout\Model\Session
     */
    protected function _getCheckout()
    {
        return $this->_objectManager->get('Magento\Checkout\Model\Session');
    }

    /**
     * @param array $data
     * @return \Kevwis\Atos\Model\Api
     */
    protected function _getApi($data = [])
    {
        return $this->_apiFactory->create([
            'data' => $data
        ]);
    }

    /**
     * @param $incrementId
     * @return \Magento\Sales\Model\Order
     */
    protected function _getOrder($incrementId)
    {
        if (!$this->_order) {
            $this->_order = $this->_orderFactory->create()->loadByIncrementId(
                $incrementId
            );
        }

        return $this->_order;
    }
}