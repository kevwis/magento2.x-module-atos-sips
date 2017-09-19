<?php
/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Block\Adminhtml\Order\View\Info;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;

/**
 * Class FraudDetails
 * @package Kevwis\Atos\Block\Adminhtml\Order\View\Info
 */
class FraudDetails extends Template
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Sales\Model\Order\Payment
     */
    public function getPayment()
    {
        $order = $this->registry->registry('current_order');
        return $order->getPayment();
    }
}
