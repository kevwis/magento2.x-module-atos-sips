<?php
/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Block\Several;

use Kevwis\Atos\Block\Standard\Info as PaymentInfo;

/**
 * Several payment info
 */
class Info extends PaymentInfo
{

    /**
     * @var string
     */
    protected $_template = 'Kevwis_Atos::payment/info/atos.phtml';
}
