<?php
/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order;

class SaveOrderAfterSubmitObserver implements ObserverInterface
{
    /**
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @param Registry $coreRegistry
     */
    public function __construct(
        Registry $coreRegistry
    ) {
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Save order into registry to use it in the overloaded controller.
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /* @var $order Order */
        $order = $observer->getEvent()->getData('order');
        $this->coreRegistry->register('direct_order', $order, true);

        return $this;
    }
}
