<?php

namespace Kevwis\Atos\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\UrlInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Kevwis\Atos\Model\Source\Cctype;
use Kevwis\Atos\Model\Source\CctypeFactory;


class ConfigProvider implements ConfigProviderInterface
{


    const PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT = 'atos_create_ba';


    /**
     * @var array
     */
    private $methodCodes = [
        Config::METHOD_STANDARD,
        Config::METHOD_SEVERAL,
        Config::METHOD_EURO,
        Config::METHOD_AURORE,
        Config::METHOD_COFIDIS3X,
        Config::METHOD_FRANFINANCE3X
    ];

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ResolverInterface
     */
    private $localeResolver;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @var \Magento\Payment\Model\Method\AbstractMethod[]
     */
    private $methods = [];

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var
     */
    private $ccTypesFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * ConfigProvider constructor.
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param ResolverInterface $localeResolver
     * @param CurrentCustomer $currentCustomer
     * @param PaymentHelper $paymentHelper
     * @param UrlInterface $urlBuilder
     * @param CctypeFactory $cctypeFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        ResolverInterface $localeResolver,
        CurrentCustomer $currentCustomer,
        PaymentHelper $paymentHelper,
        UrlInterface $urlBuilder,
        CctypeFactory $cctypeFactory,
        LoggerInterface $logger
    ) {

        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->localeResolver = $localeResolver;
        $this->currentCustomer = $currentCustomer;
        $this->paymentHelper = $paymentHelper;
        $this->urlBuilder = $urlBuilder;
        $this->ccTypesFactory = $cctypeFactory;
        $this->logger = $logger;

        foreach ($this->methodCodes as $code) {
            $this->methods[$code] = $this->paymentHelper->getMethodInstance($code);;
        }
    }

    /**
     * @return array
     */
    public function getConfig()
    {

        /** @var $ccTypes Cctype */
        $ccTypes = $this->ccTypesFactory->create();

        $config = [
            'payment' => [
                'atos' => [
                    'icons' => $ccTypes->getIcons(),
                    'availableCardTypes' => [
                        Config::METHOD_STANDARD => (array) explode(',', $this->scopeConfig->getValue('payment/atos_standard/cctypes')),
                        Config::METHOD_EURO  => (array) explode(',', $this->scopeConfig->getValue('payment/atos_euro/cctypes')),
                        Config::METHOD_SEVERAL  => (array) explode(',', $this->scopeConfig->getValue('payment/atos_several/cctypes')),
                        Config::METHOD_AURORE  => (array) explode(',', $this->scopeConfig->getValue('payment/atos_aurore/cctypes')),
                        Config::METHOD_COFIDIS3X  => (array) explode(',', $this->scopeConfig->getValue('payment/atos_cofidis3x/cctypes')),
                        Config::METHOD_FRANFINANCE3X  => (array) explode(',', $this->scopeConfig->getValue('payment/atos_franfinance3x/cctypes'))
                    ],
                    'paymentAcceptanceMarkHref' => $this->urlBuilder->getUrl('atos'),
                    'paymentAcceptanceMarkSrc' => $this->urlBuilder->getBaseUrl(UrlInterface::URL_TYPE_WEB) . 'atos/logo.png',
                    'redirectUrl' => [
                        Config::METHOD_STANDARD => $this->urlBuilder->getUrl($this->scopeConfig->getValue('payment/atos_standard/order_place_redirect_url')),
                        Config::METHOD_EURO => $this->urlBuilder->getUrl($this->scopeConfig->getValue('payment/atos_euro/order_place_redirect_url')),
                        Config::METHOD_SEVERAL => $this->urlBuilder->getUrl($this->scopeConfig->getValue('payment/atos_several/order_place_redirect_url')),
                        Config::METHOD_AURORE => $this->urlBuilder->getUrl($this->scopeConfig->getValue('payment/atos_aurore/order_place_redirect_url')),
                        Config::METHOD_COFIDIS3X => $this->urlBuilder->getUrl($this->scopeConfig->getValue('payment/atos_cofidis3x/order_place_redirect_url')),
                        Config::METHOD_FRANFINANCE3X => $this->urlBuilder->getUrl($this->scopeConfig->getValue('payment/atos_franfinance3x/order_place_redirect_url'))
                    ],
                    'billingAgreementCode' => [
                        'default' => false // self::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT
                    ],
                    'inContextConfig' => [
                        'clientConfig' => [
                            'locale' => $this->localeResolver->getLocale()
                        ]
                    ]
                ],
            ]
        ];

        return $config;
    }
}
