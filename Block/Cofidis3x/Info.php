<?php
/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Block\Cofidis3x;

use Kevwis\Atos\Block\Standard\Info as PaymentInfo;

/**
 * Cofidis3x payment info
 */
class Info extends PaymentInfo
{

    /**
     * @var string
     */
    protected $_template = 'Kevwis_Atos::payment/info/atos.phtml';
}
