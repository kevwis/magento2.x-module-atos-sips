<?php
/**
 *
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Controller\Payment;


/**
 * Class Response
 */
class Success extends AbstractController
{

    /**
     *
     * @return string
     */
    public function execute()
    {

        try {

            /* @var $session \Magento\Checkout\Model\Session */
            $session = $this->_getCheckout();
            if ((bool) $session->getLastRealOrderId()) {

                /* @var $order \Magento\Sales\Model\Order */
                $order = $this->_getOrder($session->getLastRealOrderId());
                if (!$order->isObjectNew()) {

                    $order->addStatusHistoryComment("Customer come back from Atos payment gateway with success")
                        ->setIsCustomerNotified(false)
                        ->save();

                    $this->messageManager->addSuccessMessage(__("Order's payment is pending capture"));
                    $this->_redirect('checkout/onepage/success');
                    return;

                } else {

                    throw new \Exception('Order not found.');
                }
            }

        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage(), ['exception' => $e]);
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }

        $this->_redirect('checkout/cart');
        return;
    }
}