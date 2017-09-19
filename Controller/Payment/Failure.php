<?php
/**
 *
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Controller\Payment;

use Magento\Framework\Phrase;

/**
 * Class Response
 */
class Failure extends AbstractController
{

    /**
     *
     * @return string
     */
    public function execute()
    {

        try {

            if (!$this->_isEnabled()) {
                throw new \Exception('Atos API is disabled.');
            }

            /* @var $session \Magento\Checkout\Model\Session */
            $session = $this->_getCheckout();
            if ((bool) $session->getLastRealOrderId()) {

                /* @var $order \Magento\Sales\Model\Order */
                $order = $this->_getOrder($session->getLastRealOrderId());
                if (!$order->isObjectNew()) {

                    $order
                        ->cancel()
                        ->addStatusHistoryComment("Customer come back from Atos payment gateway with failure. Order was cancelled.")
                        ->setIsCustomerNotified(false)
                        ->save();

                    $this->messageManager->addErrorMessage(new Phrase("Order's payment failed. Order '%1' was cancelled.", [$order->getIncrementId()]));
                    $this->_redirect('checkout/cart');
                    return;

                } else {

                    throw new \Exception('Order not found');
                }
            }

        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage(), ['exception' => $e]);
            $this->messageManager->addErrorMessage(new Phrase($e->getMessage()));
        }

        $this->_redirect('checkout/cart');
        return;
    }
}