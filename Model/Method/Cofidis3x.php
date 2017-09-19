<?php
/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Model\Method;


use Kevwis\Atos\Model\Config;

/**
 * Class Cofidis3x
 * @package Kevwis\Atos\Model\Method
 */
class Cofidis3x extends AbstractMethod
{
    /**
     * @var string
     */
    protected $_code = Config::METHOD_COFIDIS3X;

    /**
     * @var string
     */
    protected $_formBlockType = 'Kevwis\Atos\Block\Cofidis3x\Form';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Kevwis\Atos\Block\Cofidis3x\Info';


    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function getPaymentOptions(\Magento\Sales\Model\Order $order) {
        $billing = $order->getBillingAddress();
        return array_merge(parent::getPaymentOptions($order), [
            'data' => "\"COFIDIS_3X_DATA={$billing->getPrefix()}#{$billing->getLastname()}#{$billing->getFirstname()}##{$billing->getStreet1()}#{$billing->getStreet2()}#{$billing->getPostcode()}#{$billing->getCity()}=#FR##{$billing->getTelephone()}#0#0###3CB#WEB#FRA\""
        ]);
    }
}
