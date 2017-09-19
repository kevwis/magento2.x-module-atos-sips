<?php
/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Block\Euro;

use Kevwis\Atos\Block\Standard\Info as PaymentInfo;

/**
 * Euro payment info
 */
class Info extends PaymentInfo
{

    /**
     * @var string
     */
    protected $_template = 'Kevwis_Atos::payment/info/atos.phtml';
}
