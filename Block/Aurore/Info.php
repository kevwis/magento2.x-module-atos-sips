<?php
/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Block\Aurore;

use Kevwis\Atos\Block\Standard\Info as PaymentInfo;

/**
 * Payflow payment info
 */
class Info extends PaymentInfo
{

    /**
     * @var string
     */
    protected $_template = 'Kevwis_Atos::payment/info/atos.phtml';
}
