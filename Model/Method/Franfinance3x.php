<?php
/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Model\Method;


use Kevwis\Atos\Model\Config;

/**
 * Class Franfinance3x
 * @package Kevwis\Atos\Model\Method
 */
class Franfinance3x extends AbstractMethod
{
    /**
     * @var string
     */
    protected $_code = Config::METHOD_FRANFINANCE3X;

    /**
     * @var string
     */
    protected $_formBlockType = 'Kevwis\Atos\Block\Franfinance3x\Form';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Kevwis\Atos\Block\Franfinance3x\Info';


    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function getPaymentOptions(\Magento\Sales\Model\Order $order) {
        $billing = $order->getBillingAddress();
        return array_merge(parent::getPaymentOptions($order), [
            'data' => "\"3XCBFRANFINANCE_DATA=#a=GIV%2FDY2Vkwg%3D&b=LSjQctH5buU%3D&c=eXMS7rHnFf8%3D&d=dFVH3EP6hsU%3D#paci#######{$billing->getPrefix()}#{$billing->getLastname()}##{$billing->getFirstname()}#####{$billing->getStreet1()}#{$billing->getStreet2()}#{$billing->getPostcode()}#{$billing->getCity()}#{$billing->getTelephone()}##\""
        ]);
    }
}
