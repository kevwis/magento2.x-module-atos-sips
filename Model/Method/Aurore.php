<?php
/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Model\Method;


use Kevwis\Atos\Model\Config;

/**
 * Class Aurore
 * @package Kevwis\Atos\Model\Method
 */
class Aurore extends AbstractMethod
{
    /**
     * @var string
     */
    protected $_code = Config::METHOD_AURORE;

    /**
     * @var string
     */
    protected $_formBlockType = 'Kevwis\Atos\Block\Aurore\Form';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Kevwis\Atos\Block\Aurore\Info';


    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function getPaymentOptions(\Magento\Sales\Model\Order $order) {
        return array_merge(parent::getPaymentOptions($order), [
            'data' => "DATE_NAISSANCE={$order->getCustomerDob()},MODE_REGLEMENT=MR_CREDIT\;"
        ]);
    }
}
