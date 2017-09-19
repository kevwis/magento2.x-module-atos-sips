<?php
/**
 *
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Controller\Payment;
use Kevwis\Atos\Model\Config;

/**
 * Class Response
 */
class Response extends AbstractController
{


    /**
     * @return string
     */
    protected function _initialize() {

        try {

            /* @var $request \Magento\Framework\App\Request\Http  */
            $request = $this->getRequest();
            if ($hash = $request->getParam('DATA', false)) {

                $type = $request->getParam('type', Config::METHOD_STANDARD);

                /* @var $method \Kevwis\Atos\Model\Method\AbstractMethod */
                $method  = $this->_paymentData->getMethodInstance("atos_{$type}");

                /* @var $api \Kevwis\Atos\Model\Api */
                $api = $this->_apiFactory->create([
                    'data' => [
                        'method' => $method,
                        'type' => 'response',
                    ]
                ]);

               return $api->decrypt($hash);

            } else {
                throw new \Exception("Atos API invalid hash 'DATA'.");
            }

        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * @throws \Exception
     */
    public function execute()
    {

        try {

            if (!$this->_isEnabled()) {
                throw new \Exception('Atos API is disabled.');
            }

            /* @var $response \Kevwis\Atos\Model\Api\Response */
            if ($response = $this->_initialize()) {

                $sips = $response->getTransactionData();

                /* @var $order \Magento\Sales\Model\Order */
                $order = $this->_getOrder($sips->getOrderId());
                if (!$order->isObjectNew()) {

                    /* @var $payment \Magento\Sales\Model\Order\Payment */
                    $payment = $order->getPayment();
                    $payment->setAdditionalInformation($sips->getData());

                    /* @var $method \Kevwis\Atos\Model\Method\AbstractMethod */
                    $method = $payment->getMethodInstance();

                    if ($sips->getMerchantId() != $method->getMerchantId()) {

                        throw new \Exception('Invalid merchant ID');
                    }

                    if ($response->isValid()) {
                        switch ($sips->getCaptureMode()) {
                            case $method::PAYMENT_ACTION_CAPTURE:

                                $payment->setTransactionId($sips->getTransactionId());
                                $payment->setIsTransactionClosed(false);
                                $payment->authorize(true, $sips->getAmount() / 100);

                                $order->setStatus((string) $method->getValue('order_authorize_status'));

                                break;

                            case $method::PAYMENT_ACTION_AUTHORIZE:

                                /* @var $invoice \Magento\Sales\Model\Order\Invoice */
                                $invoice = $order->prepareInvoice();
                                $invoice->setRequestedCaptureCase($invoice::CAPTURE_OFFLINE);
                                $invoice->register();
                                $invoice->save();

                                $payment->setTransactionId($sips->getTransactionId());
                                $payment->setIsTransactionClosed(true);
                                $payment->capture($invoice);

                                $order->setStatus((string) $method->getValue('order_capture_status'));

                                break;

                            default:

                                break;
                        }

                    } else {

                        $payment->setTransactionId($sips->getTransactionId());
                        $payment->setIsTransactionClosed(true);

                        $order->setCustomerNote($response->getResponseMessage());
                        $order
                            ->cancel()
                            ->setIsCustomerNotified(true)
                            ->addStatusHistoryComment(__(
                                "Atos IPN error '%1': %2 - %3",
                                $response->getResponseMessage(),
                                $response->getBankResponseCode(),
                                $response->getBankResponseMessage()
                            ), (string) $method->getValue('order_error_status'));

                        $this->_errorSender->send($order);

                    }

                    /* @var $payment \Magento\Sales\Model\Order\Payment */
                    $payment->save();
                    $order->save();

                    return;

                } else {

                    throw new \Exception('Order ID is missing, invalid notification');
                }
            }

        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage(), ['exception' => $e]);
        }

        /* @desc lock direct access */
        $this->_redirect('checkout/cart');
        return;
    }
}
