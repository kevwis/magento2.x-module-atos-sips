<?php
/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Redirect
 * @package Kevwis\Atos\Block
 */
class Redirect extends Template
{

    /**
     * @return bool
     */
    protected function _isEnabled() {
        return (bool) $this->_scopeConfig->getValue('atos/settings/enabled');
    }


    /**
     * @return string
     */
    protected function _toHtml()
    {

        /* @var $order \Magento\Sales\Model\Order */
        $order = $this->getOrder();

        $this->_logger->debug(__FUNCTION__, [$order->getId()]);
        if (!$order->isObjectNew()) {


            /* @var $payment \Magento\Sales\Model\Order\Payment */
            $payment = $order->getPayment();

            /* @var $api \Kevwis\Atos\Model\Api */
            $api = $payment->getMethodInstance()->getApi([
                'type' => 'request',
                'order' => $order
            ]);

            /* @var $response \Kevwis\Atos\Model\Api\Response */
            $response = $api->initialize($payment->getMethodInstance());

            $this->_logger->debug(__FUNCTION__, [$response->getData()]);
            foreach ($response->getSips() as $key => $value) {

                switch ((int) $key) {
                    case 0:
                        break;

                    case 1:

                        $this->setData('code', (int) $value);
                        break;

                    case 2:
                        break;

                    case 3:

                        $this->setData('form', $value);
                        break;

                    case 4:
                        break;
                }
            }

            $this->setData('method', $payment->getMethodInstance());
        }

        return parent::_toHtml();
    }
}