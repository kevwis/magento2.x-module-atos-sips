<?php
/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Model\Method;


use Kevwis\Atos\Model\Config;

/**
 * Class Euro
 * @package Kevwis\Atos\Model\Method
 */
class Euro extends AbstractMethod
{
    /**
     * @var string
     */
    protected $_code = Config::METHOD_EURO;

    /**
     * @var string
     */
    protected $_formBlockType = 'Kevwis\Atos\Block\Euro\Form';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Kevwis\Atos\Block\Euro\Info';


    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function getPaymentOptions(\Magento\Sales\Model\Order $order) {
        $billing = $order->getBillingAddress();
        return array_merge(parent::getPaymentOptions($order), [
            'data' => "\"1EUROCOM_DATA={$billing->getPrefix()}#{$billing->getLastname()}#{$billing->getFirstname()}##{$billing->getStreet1()}#{$billing->getStreet2()}#{$billing->getPostcode()}#{$billing->getCity()}=#FR##{$billing->getTelephone()}#0#0#12##1EU#WEB\""
        ]);
    }
}
