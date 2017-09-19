<?php
/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Model\Method;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Quote\Api\Data\PaymentInterface;
use Kevwis\Atos\Model\ApiFactory;
use Magento\Checkout\Model\Session;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedExceptionFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\Logger;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Kevwis\Atos\Model\Config;
use Kevwis\Atos\Model\ConfigFactory;

/**
 * Class Standard
 * @package Kevwis\Atos\Model\Method
 */
abstract class AbstractMethod extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * Atos capture action
     *
     * @var string
     */
    const PAYMENT_ACTION_CAPTURE = 'AUTHOR_CAPTURE';

    /**
     * Atos validation action
     *
     * @var string
     */
    const PAYMENT_ACTION_AUTHORIZE = 'VALIDATION';

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_isInitializeNeeded = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isGateway = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canOrder = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canAuthorize = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canCapture = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canCapturePartial = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canRefund = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canVoid = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canUseInternal = false;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canUseCheckout = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canFetchTransactionInfo = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canReviewPayment = true;

    /**
     * @var ApiFactory
     */
    protected $_apiFactory;

    /**
     * @var array
     */
    protected $_api = [];

    /**
     * Payment additional information key for payment action
     *
     * @var string
     */
    protected $_isOrderPaymentActionKey = 'is_order_action';

    /**
     * Payment additional information key for number of used authorizations
     *
     * @var string
     */
    protected $_authorizationCountKey = 'authorization_count';

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;


    /**
     * @var ConfigFactory
     */
    protected $_configFactory;


    /**
     * @var Session
     */
    protected $_checkoutSession;

    /**
     * @var LocalizedExceptionFactory
     */
    protected $_exception;

    /**
     * @var
     */
    private $config;

    /**
     * @var TransactionRepositoryInterface
     */
    protected $transactionRepository;

    /**
     * @var BuilderInterface
     */
    protected $transactionBuilder;

    /**
     * @var OrderSender
     */
    protected $_orderSender;

    /**
     * @var InvoiceSender
     */
    protected $_invoiceSender;


    /**
     * Direct constructor.
     * @param OrderSender $orderSender
     * @param InvoiceSender $invoiceSender
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param Data $paymentData
     * @param ScopeConfigInterface $scopeConfig
     * @param Logger $logger
     * @param ApiFactory $apiFactory
     * @param StoreManagerInterface $storeManager
     * @param UrlInterface $urlBuilder
     * @param ConfigFactory $configFactory
     * @param Session $checkoutSession
     * @param LocalizedExceptionFactory $exception
     * @param TransactionRepositoryInterface $transactionRepository
     * @param BuilderInterface $transactionBuilder
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        OrderSender $orderSender,
        InvoiceSender $invoiceSender,
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Data $paymentData,
        ScopeConfigInterface $scopeConfig,
        Logger $logger,
        ApiFactory $apiFactory,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        ConfigFactory $configFactory,
        Session $checkoutSession,
        LocalizedExceptionFactory $exception,
        TransactionRepositoryInterface $transactionRepository,
        BuilderInterface $transactionBuilder,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {

        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );

        $this->_apiFactory = $apiFactory;
        $this->_storeManager = $storeManager;
        $this->_urlBuilder = $urlBuilder;
        $this->_configFactory = $configFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_exception = $exception;
        $this->transactionRepository = $transactionRepository;
        $this->transactionBuilder = $transactionBuilder;
        $this->_orderSender = $orderSender;
        $this->_invoiceSender = $invoiceSender;
    }


    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate()
    {
        /* @var $paymentInfo Payment */
        $paymentInfo = $this->getInfoInstance();

        $min = (float) $this->getConfigData('min_order_total');
        $max = (float) $this->getConfigData('max_order_total');

        if ($paymentInfo instanceof Payment) {
            /* @var $entity \Magento\Sales\Model\Order */
            $entity = $paymentInfo->getOrder();
        } else {
            /* @var $entity \Magento\Quote\Model\Quote */
            $entity = $paymentInfo->getQuote();
        }

        if (((bool) $min && (float) $entity->getGrandTotal() < $min)
            && ((bool) $max && (float) $entity->getGrandTotal() > $max)) {

            throw new \Magento\Framework\Exception\LocalizedException(
                __('You can\'t use the payment type you selected to make payments.')
            );
        }

        if (parent::validate()) {
            return true;
        }
    }


    public function initialize($paymentAction, $stateObject)
    {

        /* @var $order \Magento\Sales\Model\Order */
        $order = $this->getInfoInstance()->getOrder();

        switch ($paymentAction) {
            case  \Magento\Payment\Model\Method\AbstractMethod::ACTION_ORDER:

                $stateObject->addData([
                    'is_notified' => false,
                    'state' => $order::STATE_PENDING_PAYMENT
                ]);

            default:
        }

        return $this;
    }



    /**
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     */
    public function order(InfoInterface $payment, $amount)
    {
        parent::order($payment, $amount);
        return $this;
    }


    /**
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     */
    public function authorize(InfoInterface $payment, $amount)
    {
        # $order = $payment->getOrder();
        # $this->_orderSender->send($order);
        parent::authorize($payment, $amount);
        return $this;
    }

    /**
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     */
    public function capture(InfoInterface $payment, $amount)
    {
        parent::capture($payment, $amount);
        return $this;
    }


    /**
     * @param InfoInterface $payment
     * @return $this
     */
    public function void(InfoInterface $payment)
    {
        parent::void($payment);
        return $this;
    }


    /**
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this
     */
    public function refund(InfoInterface $payment, $amount)
    {
        parent::refund($payment, $amount);
        return $this;
    }


    /**
     * Getter for specified value according to set payment method code
     *
     * @param mixed $key
     * @param null $storeId
     * @return mixed
     */
    public function getValue($key, $storeId = null)
    {
        return $this->getConfig()->getValue($key, $storeId);
    }

    /**
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return $this->getConfig()->getUrl(Config::ORDER_PLACE_REDIRECT_URL);
    }

    /**
     * @return string
     */
    public function getNormalReturnUrl()
    {
        return $this->getConfig()->getUrl(Config::NORMAL_RESPONSE_URL);
    }

    /**
     * @return string
     */
    public function getAutomaticReturnUrl()
    {
        return $this->getConfig()->getUrl(Config::AUTOMATIC_RESPONSE_URL);
    }

    /**
     * @return string
     */
    public function getCancelReturnUrl()
    {
        return $this->getConfig()->getUrl(Config::CANCEL_RESPONSE_URL);
    }

    /**
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function getApi(array $data = [])
    {
        if (isset($data['type'])) {
            switch ($data['type']) {
                default:
                    if (!isset($this->_api[$data['type']])) {
                        $this->_api[$data['type']] = $this->_apiFactory->create([
                            'data' => array_merge($data, [
                                'method' => $this
                            ])
                        ]);
                    }
            }

        } else {

            throw new LocalizedException(new Phrase('API type is missing'));
        }

        return $this->_api[$data['type']];
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        if (!$this->config) {
            $this->config = $this->_configFactory->create();
            $this->config->setStoreId($this->getStore());
            $this->config->setMethodInstance($this);
            $this->config->setMethod($this);
        }

        return $this->config;
    }


    /**
     * @param DataObject $data
     * @return $this
     */
    public function assignData(DataObject $data)
    {

        parent::assignData($data);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_object($additionalData)) {
            $additionalData = new DataObject($additionalData ?: []);
        }

        /** @var $info DataObject */
        $info = $this->getInfoInstance();
        $info->addData(
            [
                'cc_type' => $additionalData->getCcType(),
            ]
        );

        return $this;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function getPaymentOptions(\Magento\Sales\Model\Order $order) {
        return array_merge($this->getConfig()->getPaymentOptions($order), [
            "customer_id" => $order->getCustomerId(),
            "customer_email" => $order->getCustomerEmail(),
            "customer_ip_address" => $order->getRemoteIp(),
            "order_id"  => $order->getIncrementId(),
            "caddie" => $order->getQuoteId(),
            "amount" => (int) number_format($order->getGrandTotal(), 2, '', ' ')
        ]);
    }

    /**
     * @return string
     */
    public function getCaptureDay() {
        return $this->getConfig()->getCaptureDay();
    }

    /**
     * @return string
     */
    public function getCaptureMode() {
        return $this->getConfig()->getCaptureMode();

    }

    /**
     * @return string
     */
    public function getConfigPaymentAction()
    {
        return $this->getConfig()->getPaymentAction();
    }

    /**
     * @return string
     */
    public function getPaymentMeans()
    {
        return $this->getConfig()->getPaymentMeans();
    }

    /**
     * @return mixed
     */
    public function getDataFieldKeys()
    {
        return $this->getConfig()->getDataFieldKeys();
    }

    /**
     * @return mixed
     */
    public function getSelectedDataFieldKeys()
    {
        return $this->getConfig()->getSelectedDataFieldKeys();
    }

    /**
     * @return bool|string
     */
    public function getMerchantId() {
        return $this->getConfig()->getMerchantId();
    }

    /**
     * @return bool|string
     */
    public function getCurrencyCode() {
        return $this->getConfig()->getCurrencyCode();
    }

    /**
     * @return bool|string
     */
    public function hasDebug() {
        return $this->getConfig()->hasDebug();
    }
}
