<?php
/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Block\Franfinance3x;

use Kevwis\Atos\Block\Standard\Info as PaymentInfo;

/**
 * Franfinance3x payment info
 */
class Info extends PaymentInfo
{

    /**
     * @var string
     */
    protected $_template = 'Kevwis_Atos::payment/info/atos.phtml';
}
