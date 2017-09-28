<?php
/**
 * Copyright © 2017 Kev WIS All rights reserved.
 */

// @codingStandardsIgnoreFile

namespace Kevwis\Atos\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Helper\Formatter;
use Magento\Payment\Model\Method\ConfigInterface;
use Magento\Payment\Model\MethodInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Config
 * @package Magento\Paypal\Model
 */
class Config implements ConfigInterface
{

    use Formatter;

    /**
     * Atos SIPS
     */
    const METHOD_STANDARD = 'atos_standard';

    /**
     * Atos EURO
     */
    const METHOD_EURO = 'atos_euro';

    /**
     * Atos Several
     */
    const METHOD_SEVERAL = 'atos_several';

    /**
     * Atos AURORE
     */
    const METHOD_AURORE = 'atos_aurore';

    /**
     * Atos Cofidis 3x
     */
    const METHOD_COFIDIS3X = 'atos_cofidis3x';

    /**
     * Atos Franfinace 3x CB
     */
    const METHOD_FRANFINANCE3X = 'atos_franfinance3x';

    /**
     * config node for automatic response url
     */
    const AUTOMATIC_RESPONSE_URL = 'automatic_response_url';

    /**
     * config node for normal response url
     */
    const NORMAL_RESPONSE_URL = 'normal_return_url';

    /**
     * config node for cancel response url
     */
    const CANCEL_RESPONSE_URL = 'cancel_return_url';

    /**
     * config node for order place redirect url
     */
    const ORDER_PLACE_REDIRECT_URL = 'order_place_redirect_url';

    /**
     * Current payment method code
     *
     * @var string
     */
    private $methodCode;

    /**
     * Current store id
     *
     * @var int
     */
    private $storeId;

    /**
     * @var string
     */
    private $pathPattern;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;


    /**
     * @var MethodInterface
     */
    private $methodInstance;


    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }


    /**
     * @return bool|string
     */
    public function getPathFile() {
        if ((bool) $this->getValue('pathfile')) {
            $pathfile = realpath(BP . DIRECTORY_SEPARATOR . $this->getValue('pathfile'));
            if (file_exists($pathfile)) {
                return $pathfile;
            }
        }
    }

    /**
     * @return bool|string
     */
    public function getRequestBinFile() {
        return realpath(BP . DIRECTORY_SEPARATOR . $this->scopeConfig->getValue('atos/settings/bin_request', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $this->storeManager->getStore()->getId()));
    }

    /**
     * @return bool|string
     */
    public function getResponseBinFile() {
        return realpath(BP . DIRECTORY_SEPARATOR . $this->scopeConfig->getValue('atos/settings/bin_response', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $this->storeManager->getStore()->getId()));
    }


    /**
     * @return bool|string
     */
    public function getLanguage() {
        return strtolower( substr( $this->scopeConfig->getValue('general/locale/code', ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $this->storeManager->getStore()->getId()), 9, 2) );
    }


    /**
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return $this->getUrl(Config::ORDER_PLACE_REDIRECT_URL);
    }

    /**
     * @return string
     */
    public function getNormalReturnUrl()
    {
        return $this->getUrl(Config::NORMAL_RESPONSE_URL);
    }

    /**
     * @return string
     */
    public function getAutomaticReturnUrl()
    {
        return $this->getUrl(Config::AUTOMATIC_RESPONSE_URL);
    }

    /**
     * @return string
     */
    public function getCancelReturnUrl()
    {
        return $this->getUrl(Config::CANCEL_RESPONSE_URL);
    }

    /**
     * @param string $type
     * @return string
     */
    public function getUrl($type)
    {
        return $this->storeManager->getStore($this->methodInstance->getStore())
            ->getUrl($this->getValue($type));
    }


    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function getPaymentOptions(\Magento\Sales\Model\Order $order) {

        $data = [
            "normal_return_url" => $this->getNormalReturnUrl(),
            "cancel_return_url" => $this->getCancelReturnUrl(),
            "automatic_response_url" => $this->getAutomaticReturnUrl(),
            "merchant_country" => strtolower($order->getBillingAddress()->getCountryId()),
            "merchant_id" => $this->getMerchantId(),
            "language" => $this->getLanguage(),
            "currency_code" => $this->getCurrencyCode(),
            "payment_means" => $this->getPaymentMeans(),
        ];

        if ($this->getCaptureMode()) {
            $data['capture_mode'] = $this->getCaptureMode();
        }

        if ($this->getCaptureDay() > 0) {
            $data['capture_day'] = $this->getCaptureDay();
        }

        return $data;
    }


    /**
     * @return bool|string
     */
    public function getPaymentAction() {
        return $this->getValue('payment_action');
    }

    /**
     * @return string
     */
    public function getCaptureDay() {
        return (int) $this->getValue('capture_day');
    }

    /**
     * @return string
     */
    public function getCaptureMode() {
        return (string) $this->getValue('capture_mode');
    }

    /**
     * @return string
     */
    public function getPaymentMeans()
    {
        return str_replace(',', ',2,',  $this->getValue('cctypes')) . ',2';
    }

    /**
     * @return mixed
     */
    public function getDataFieldKeys()
    {
        return $this->getValue('data_field');
    }

    /**
     * @return mixed
     */
    public function getSelectedDataFieldKeys()
    {
        return str_replace(',', '\;', $this->getDataFieldKeys());
    }

    /**
     * @return bool|string
     */
    public function getMerchantId() {
        return (string) $this->getValue('merchant_id');
    }

    /**
     * @return bool|string
     */
    public function getCurrencyCode() {
        return (string) $this->getValue('currency_code');
    }

    /**
     * @return bool|string
     */
    public function hasDebug() {
        return (bool) $this->getValue('debug');
    }

    /**
     * @param string $field
     * @param null $storeId
     * @return mixed
     */
    public function getValue($field, $storeId = null) {
        switch ($field) {
            default:
                $underscored = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $field));
                $path = $this->_getSpecificConfigPath($underscored);
                if ($path !== null) {
                    $value = $this->scopeConfig->getValue($path, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $storeId);
                    $value = $this->_prepareValue($underscored, $value);
                    return $value;
                }
        }
    }

    /**
     * Store ID setter
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = (int)$storeId;
        return $this;
    }

    /**
     * Sets method instance used for retrieving method specific data
     *
     * @param MethodInterface $method
     * @return $this
     */
    public function setMethodInstance($method)
    {
        $this->methodInstance = $method;
        return $this;
    }

    /**
     * Method code setter
     *
     * @param string|MethodInterface $method
     * @return $this
     */
    public function setMethod($method)
    {
        if ($method instanceof MethodInterface) {
            $this->setMethodCode($method->getCode());
        } elseif (is_string($method)) {
            $this->setMethodCode($method);
        }
        return $this;
    }

    /**
     * Payment method instance code getter
     *
     * @return string
     */
    public function getMethodCode()
    {
        return $this->methodCode;
    }

    /**
     * Sets method code
     *
     * @param string $methodCode
     * @return void
     */
    public function setMethodCode($methodCode)
    {
        $this->methodCode = $methodCode;
    }

    /**
     * Sets path pattern
     *
     * @param string $pathPattern
     * @return void
     */
    public function setPathPattern($pathPattern)
    {
        $this->pathPattern = $pathPattern;
    }

    /**
     * Map any supported payment method into a config path by specified field name
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _getSpecificConfigPath($fieldName)
    {
        if ($this->pathPattern) {
            return sprintf($this->pathPattern, $this->methodCode, $fieldName);
        }
        return "payment/{$this->methodCode}/{$fieldName}";
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function _prepareValue($key, $value)
    {
        return $value;
    }
}
