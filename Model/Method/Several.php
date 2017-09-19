<?php
/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Model\Method;


use Kevwis\Atos\Model\Config;

/**
 * Class Several
 * @package Kevwis\Atos\Model\Method
 */
class Several extends AbstractMethod
{
    /**
     * @var string
     */
    protected $_code = Config::METHOD_SEVERAL;

    /**
     * @var string
     */
    protected $_formBlockType = 'Kevwis\Atos\Block\Several\Form';

    /**
     * @var string
     */
    protected $_infoBlockType = 'Kevwis\Atos\Block\Several\Info';

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function getPaymentOptions(\Magento\Sales\Model\Order $order) {
        $data = array_merge(parent::getPaymentOptions($order), [
            'data' => "NB_PAYMENT={$this->getNbPayment()}\;PERIOD=30\;INITIAL_AMOUNT={$this->getFirstAmount($order)}\;{{$this->getSelectedDataFieldKeys()}"
        ]);

        return $data;
    }

    /**
     * @return int
     */
    public function getNbPayment() {
        return $this->getValue('nb_payment') ? (int) $this->getValue('nb_payment'): 1;
    }

    /**
     * @param $order
     * @return float|int
     */
    public function getFirstAmount($order) {
        $amount = (int) number_format($order->getGrandTotal(), 2, '', ' ');
        return $amount / ($this->getNbPayment() ? (int) $this->getNbPayment() : 1);
    }
}
