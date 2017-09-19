<?php
/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Model\Method;


use Kevwis\Atos\Model\Config;

/**
 * Class Standard
 * @package Kevwis\Atos\Model\Method
 */
class Standard extends AbstractMethod
{
    /**
     * @var string
     */
    protected $_code = Config::METHOD_STANDARD;

    /**
     * @var string
     */
    protected $_formBlockType = 'Kevwis\Atos\Block\Standard\Form';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Kevwis\Atos\Block\Standard\Info';


    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function getPaymentOptions(\Magento\Sales\Model\Order $order) {
        return array_merge(parent::getPaymentOptions($order), [
            'data' => $this->getSelectedDataFieldKeys()
        ]);
    }
}