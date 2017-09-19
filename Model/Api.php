<?php

namespace Kevwis\Atos\Model;

use Magento\Framework\DataObject;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Api extends DataObject
{

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Api\RequestFactory
     */
    private $requestFactory;

    /**
     * @var Api\ResponseFactory
     */
    private $responseFactory;

    /**
     * @var Api\ResponseFactory
     */
    private $storeManager;


    /**
     * Api constructor.
     * @param array $data
     * @param Api\RequestFactory $requestFactory
     * @param Api\ResponseFactory $responseFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param DataObjectFactory $dataObjectFactory
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        $data = [],
        Api\RequestFactory $requestFactory,
        Api\ResponseFactory $responseFactory,
        ScopeConfigInterface $scopeConfig,
        DataObjectFactory $dataObjectFactory,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger)
    {

        $this->scopeConfig = $scopeConfig;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->requestFactory = $requestFactory;
        $this->responseFactory = $responseFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;

        parent::__construct($data);
    }

    /**
     * @param $data
     * @return Api\Request
     */
    protected function _prepareRequest($data, $options = array()) {
        $request = $this->requestFactory->create([
            'data' => $data,
            'options' => array_merge($options, [
                'api' => $this
            ])
        ]);

        return $request;
    }

    /**
     * @param $data
     * @param array $options
     * @return mixed
     */
    protected function _prepareResponse($data, $options = array()) {
        $response = $this->responseFactory->create([
            'data' => $data,
            'options' => array_merge($options, [
                'api' => $this
            ])
        ]);

        return $response;
    }

    /**
     * @param Api\Request $request
     * @return string
     * @throws LocalizedException
     */
    protected function _execute(\Kevwis\Atos\Model\Api\Request $request) {

        if ($request->isEmpty()) {
            throw new LocalizedException(
                __('Invalid Atos binary params')
            );
        }

        $message = $request->execute($this->getBinFile(), $this->getPathFile());
        return $this->responseFactory->create([
            'data' => [
                'sips' => explode ("!", $message)
            ]
        ]);
    }


    /**
     * @param Method\AbstractMethod|null $method
     * @return string
     */
    public function initialize(Method\AbstractMethod $method = null) {

        try {

            $this->logger->debug(__FUNCTION__, []);
            /* @var $payment \Magento\Sales\Model\Order */
            $order = $this->getOrder();

            /* @var $options \Kevwis\Atos\Model\Api\Request */
            $request = $this->_prepareRequest([
                'args' => array_merge($method->getPaymentOptions($order), [

                ])
            ]);

            $this->logger->debug('_prepareRequest', [$request->getData()]);
            /* @var $response \Kevwis\Atos\Model\Api\Response */
            return $this->_execute($request);

        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }
    }


    /**
     * @param $message
     * @return string
     */
    public function decrypt($message) {

        $this->logger->debug('_prepareRequest', [$message]);
        if (is_string($message)) {

            /* @var $response \Kevwis\Atos\Model\Api\Request */
            $request = $this->_prepareRequest([
                'args' => [
                    'message' => $message
                ]
            ]);

            $this->logger->debug('_prepareRequest', [$request->getData()]);
            /* @var $response \Kevwis\Atos\Model\Api\Response */
            return $this->_execute($request);
        }
    }


    /**
     * @return mixed
     */
    public function getPathFile() {
        return $this->getConfig()->getPathFile();
    }

    /**
     * @return mixed
     */
    public function getBinFile() {
        return call_user_func([$this->getConfig(), "get{$this->getType()}BinFile"]);
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->getData('type');
    }

    /**
     * @return array|Method\AbstractMethod
     */
    public function getMethod() {
        return $this->getData('method');
    }

    /**
     * @return \Kevwis\Atos\Model\Config
     */
    public function getConfig() {
        return $this->getMethod()->getConfig();
    }
}