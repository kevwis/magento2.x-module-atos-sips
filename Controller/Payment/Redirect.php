<?php
/**
 *
 * Copyright © 2017 Kev WIS All rights reserved.
 */
namespace Kevwis\Atos\Controller\Payment;


/**
 * Class Redirect
 */
class Redirect extends AbstractController
{


    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {

        try {

            /* @var $session \Magento\Checkout\Model\Session */
            $session = $this->_getCheckout();

            $this->_logger->debug(__CLASS__, [ $session->getData()]);
            if ((bool) $session->getLastRealOrderId()) {

                $resultPage = $this->_resultPageFactory->create();
                $resultPage->getConfig()->getTitle()->prepend(__(' Atos SIPS '));

                if ($block = $resultPage->getLayout()->getBlock('atos.form.redirect')) {
                    /* @var $order \Magento\Sales\Model\Order */
                    $order = $this->_getOrder($session->getLastRealOrderId());
                    if ($order->isObjectNew()) {
                        throw new \Exception('Order not found.');
                    }

                    $block->addData([
                        'order' => $order
                    ]);
                }

                return $resultPage;
            }

        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage(), ['exception' => $e]);
        }

        $this->_redirect('checkout/cart');
    }
}
