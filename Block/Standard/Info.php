<?php
/**
 * Copyright © 2017 Kev WIS All rights reserved.
 *
 */
namespace Kevwis\Atos\Block\Standard;

use \Magento\Payment\Block\Info\Cc as PaymentInfo;

/**
 * Payflow payment info
 */
class Info extends PaymentInfo
{

    /**
     * @var string
     */
    protected $_template = 'Kevwis_Atos::payment/info/atos.phtml';

    /**
     * @return array
     */
    public function getSpecificInformation()
    {
        $data = parent::_prepareSpecificInformation();
        foreach ($this->getMethod()->getInfoInstance()->getAdditionalInformation() as $key => $value) {
            switch ($key) {
                case 'debug':
                    break;
                default:
                    $data[$key] = $value;
            }
        }

        return $this->_prepareSpecificInformation($data)->getData();
    }

}
