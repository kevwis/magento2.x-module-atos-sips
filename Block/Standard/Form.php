<?php
/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Block\Standard;

use Kevwis\Atos\Model\Config;
use Kevwis\Atos\Model\ConfigFactory;
use Kevwis\Atos\Helper\Data;
use Magento\Payment\Block\Form as PaymentForm;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\View\Element\Template\Context;


/**
 * Class Form
 * @package Kevwis\Atos\Block\Standard
 */
class Form extends PaymentForm
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_methodCode = Config::METHOD_STANDARD;


    /**
     * @var Data
     */
    protected $_atosData;

    /**
     * @var ConfigFactory
     */
    protected $_atosConfigFactory;

    /**
     * @var ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @var null
     */
    protected $_config;

    /**
     * @var bool
     */
    protected $_isScopePrivate;

    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * Form constructor.
     * @param Context $context
     * @param ConfigFactory $atosConfigFactory
     * @param ResolverInterface $localeResolver
     * @param Data $atosData
     * @param CurrentCustomer $currentCustomer
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigFactory $atosConfigFactory,
        ResolverInterface $localeResolver,
        Data $atosData,
        CurrentCustomer $currentCustomer,
        array $data = []
    ) {
        $this->_atosData = $atosData;
        $this->_atosConfigFactory = $atosConfigFactory;
        $this->_localeResolver = $localeResolver;
        $this->_config = null;
        $this->_isScopePrivate = true;
        $this->currentCustomer = $currentCustomer;
        parent::__construct($context, $data);
    }

    /**
     * Set template and redirect message
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_config = $this->_atosConfigFactory->create()
            ->setMethod($this->getMethodCode());

        parent::_construct();
    }

    /**
     * Payment method code getter
     *
     * @return string
     */
    public function getMethodCode()
    {
        return $this->_methodCode;
    }
}